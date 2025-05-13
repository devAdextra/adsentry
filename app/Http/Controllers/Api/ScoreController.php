<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ScoreController extends Controller
{
    private const CACHE_TTL = 3600; // 1 ora
    private const CACHE_TTL_TOTAL = 86400; // 24 ore per i totali
    
    public function getDistribution(Request $request)
    {
        try {
            $filters = $request->only(['macro', 'micro', 'nano', 'extra']);
            
            // Rimuoviamo temporaneamente la cache
            try {
                // Query per il totale dei leads (senza cache)
                $totalLeads = DB::select("SELECT COUNT(DISTINCT user_id) as total FROM movements")[0]->total;

                // Ottimizzazione della query
                $query = "WITH FilteredUsers AS (
                    SELECT DISTINCT m.user_id
                    FROM movements m
                    USE INDEX (idx_user_filters)
                    WHERE 1=1";
                
                $params = [];
                
                // Applica i filtri
                foreach ($filters as $field => $value) {
                    if (!empty($value) && $value !== 'Tutti') {
                        $cleanValue = trim(explode('(', $value)[0]);
                        $query .= " AND m.$field = ?";
                        $params[] = $cleanValue;
                    }
                }

                $query .= "),
                    UserActions AS (
                        SELECT 
                            m.user_id,
                            MAX(m.db) as db,
                            COUNT(DISTINCT CASE WHEN m.timeActionOpen IS NOT NULL THEN m.timeActionOpen END) as open_count,
                            COUNT(DISTINCT CASE WHEN m.timeActionClick IS NOT NULL THEN m.timeActionClick END) as click_count
                        FROM movements m
                        INNER JOIN FilteredUsers fu ON m.user_id = fu.user_id
                        GROUP BY m.user_id
                    ),
                    ScoreCalculation AS (
                        SELECT 
                            user_id,
                            CASE
                                WHEN (open_count + (click_count * 2)) = 0 THEN 1
                                WHEN (open_count + (click_count * 2)) <= 5 THEN 2
                                WHEN (open_count + (click_count * 2)) <= 10 THEN 3
                                WHEN (open_count + (click_count * 2)) <= 20 THEN 4
                                WHEN (open_count + (click_count * 2)) <= 35 THEN 5
                                WHEN (open_count + (click_count * 2)) <= 50 THEN 6
                                WHEN (open_count + (click_count * 2)) <= 75 THEN 7
                                WHEN (open_count + (click_count * 2)) <= 100 THEN 8
                                ELSE 9
                            END as score_group
                        FROM UserActions
                    )
                    SELECT 
                        score_group as score,
                        COUNT(*) as count
                    FROM ScoreCalculation
                    GROUP BY score_group
                    ORDER BY score_group DESC";

                \Log::info('Query ottimizzata:', ['query' => $query, 'params' => $params]);

                $results = DB::select($query, $params);
                
                // Calcola il totale filtrato
                $filteredTotal = array_sum(array_column($results, 'count'));
                
                // Prepara la distribuzione totale
                $totalDistribution = collect($results)->mapWithKeys(function($item) use ($filteredTotal) {
                    return [$item->score => [
                        'count' => $item->count,
                        'percentage' => $filteredTotal > 0 ? round(($item->count / $filteredTotal) * 100, 2) : 0
                    ]];
                });

                // Assicurati che tutti i punteggi da 1 a 9 siano presenti
                $mainScores = collect(range(1, 9))->mapWithKeys(function($score) use ($totalDistribution) {
                    return [$score => $totalDistribution[$score] ?? ['count' => 0, 'percentage' => 0]];
                });

                // Ottieni la lista dei db unici tra i movimenti filtrati
                $dbList = Movement::query()
                    ->whereIn('user_id', function($query) use ($filters) {
                        $query->select('user_id')
                            ->from('movements')
                            ->where(function($query) use ($filters) {
                                foreach ($filters as $field => $value) {
                                    if (!empty($value) && $value !== 'Tutti') {
                                        $cleanValue = trim(explode('(', $value)[0]);
                                        $query->orWhere($field, $cleanValue);
                                    }
                                }
                            });
                    })
                    ->distinct()
                    ->pluck('db')
                    ->filter()
                    ->unique()
                    ->values();

                return response()->json([
                    'success' => true,
                    'total_distribution' => $totalDistribution,
                    'total_leads' => $totalLeads,
                    'filtered_leads' => $filteredTotal,
                    'scores' => $mainScores->values()->toArray(),
                    'db_list' => $dbList
                ]);

            } catch (\Exception $e) {
                \Log::error('Errore nell\'esecuzione della query:', [
                    'message' => $e->getMessage(),
                    'query' => $query ?? null,
                    'params' => $params ?? []
                ]);
                throw $e;
            }

        } catch (\Exception $e) {
            \Log::error('Errore in getDistribution', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Errore durante il recupero dei dati'
            ], 500);
        }
    }

    public function getScoreDetails(Request $request, $score)
    {
        try {
            $query = Movement::query()
                ->join('leads', 'movements.user_id', '=', 'leads.id')
                ->select(
                    'leads.email',
                    'movements.db',
                    DB::raw('COUNT(DISTINCT CASE WHEN movements.timeActionOpen IS NOT NULL THEN movements.timeActionOpen END) as opens'),
                    DB::raw('COUNT(DISTINCT CASE WHEN movements.timeActionClick IS NOT NULL THEN movements.timeActionClick END) as clicks')
                )
                ->groupBy('leads.email', 'movements.db')
                ->having(DB::raw('(opens + (clicks * 2))'), '>=', $this->getScoreMinValue($score))
                ->having(DB::raw('(opens + (clicks * 2))'), '<=', $this->getScoreMaxValue($score))
                ->limit(100);

            return response()->json([
                'success' => true,
                'details' => $query->get()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Metodo per invalidare la cache quando necessario
    public function invalidateCache()
    {
        Cache::tags(['scores'])->flush();
    }

    public function downloadFilteredLeads(Request $request)
    {
        try {
            $filters = $request->only(['macro', 'micro', 'nano', 'extra']);
            
            // Nome del file
            $filename = 'leads_export_' . date('Ymd_His') . '.csv';
            $handle = fopen($filename, 'w');
            
            // Intestazione
            fputcsv($handle, ['Email', 'DB']);
            
            // Processa i risultati in batch
            $offset = 0;
            $batchSize = 1000;
            
            do {
                $query = "WITH FilteredMovements AS (
                    SELECT m.user_id, m.db
                    FROM movements m
                    WHERE m.db IS NOT NULL";
                
                $params = [];
                
                // Aggiungi i filtri
                foreach ($filters as $field => $value) {
                    if (!empty($value) && $value !== 'Tutti') {
                        $query .= " AND m.$field = ?";
                        $params[] = trim(explode('(', $value)[0]);
                    }
                }
                
                $query .= ")
                SELECT DISTINCT l.email, fm.db
                FROM FilteredMovements fm
                JOIN leads l ON fm.user_id = l.id
                ORDER BY l.email, fm.db
                LIMIT ? OFFSET ?";
                
                $params[] = $batchSize;
                $params[] = $offset;
                
                $results = DB::select($query, $params);
                
                // Scrivi i risultati nel file
                foreach ($results as $row) {
                    fputcsv($handle, [$row->email, $row->db]);
                }
                
                $offset += $batchSize;
                $hasMore = count($results) === $batchSize;
                
            } while ($hasMore);
            
            fclose($handle);
            
            return response()->download($filename)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            \Log::error('Errore nel download delle lead:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
