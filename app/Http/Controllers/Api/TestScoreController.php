<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestScoreController extends Controller
{
    public function getFilterOptions(Request $request)
    {
        try {
            $column = $request->input('column');
            if (!$column || !in_array($column, ['macro', 'micro_scores'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Colonna non valida'
                ], 400);
            }
            $filters = $request->except('column');

            $query = DB::table('leads_score')
                ->select($column, DB::raw('COUNT(*) as count'))
                ->groupBy($column);

            // Applica i filtri
            foreach ($filters as $key => $value) {
                if ($value && $value !== 'Tutti') {
                    $query->where($key, $value);
                }
            }

            $results = $query->get();

            return response()->json([
                'success' => true,
                'results' => $results
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getFilterOptions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Errore nel recupero delle opzioni',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getMonthlyStats(Request $request)
    {
        try {
            $filters = $request->all();
            
            $query = DB::table('leads_score')
                ->select(
                    DB::raw('DATE_FORMAT(updated_at, "%Y-%m") as month'),
                    DB::raw('COUNT(DISTINCT user_id) as count')
                )
                ->groupBy('month')
                ->orderBy('month');

            // Applica i filtri
            foreach ($filters as $key => $value) {
                if ($value && $value !== 'Tutti') {
                    $query->where($key, $value);
                }
            }

            $results = $query->get();

            return response()->json([
                'success' => true,
                'months' => $results->pluck('month'),
                'counts' => $results->pluck('count')
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getMonthlyStats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Errore nel recupero delle statistiche mensili'
            ], 500);
        }
    }

    public function getUniqueLeads(Request $request)
    {
        try {
            $filters = $request->all();
            
            $query = DB::table('leads_score')
                ->select(DB::raw('COUNT(DISTINCT user_id) as unique_leads'));

            // Applica i filtri
            foreach ($filters as $key => $value) {
                if ($value && $value !== 'Tutti') {
                    $query->where($key, $value);
                }
            }

            $result = $query->first();

            return response()->json([
                'success' => true,
                'uniqueLeads' => $result->unique_leads
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getUniqueLeads: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Errore nel recupero del numero di lead unici'
            ], 500);
        }
    }

    public function getScoreDistribution(Request $request)
    {
        try {
            $filters = $request->all();
            
            $query = DB::table('leads_score')
                ->select(
                    'total_score',
                    DB::raw('COUNT(*) as count'),
                    DB::raw('COUNT(*) * 100.0 / (SELECT COUNT(*) FROM leads_score) as percentage')
                )
                ->groupBy('total_score')
                ->orderBy('total_score');

            // Applica i filtri
            foreach ($filters as $key => $value) {
                if ($value && $value !== 'Tutti') {
                    $query->where($key, $value);
                }
            }

            $results = $query->get();

            // Prepara l'array dei punteggi
            $scores = array_fill(0, 9, ['count' => 0, 'percentage' => 0]);
            foreach ($results as $result) {
                if ($result->total_score >= 1 && $result->total_score <= 9) {
                    $scores[$result->total_score - 1] = [
                        'count' => $result->count,
                        'percentage' => round($result->percentage, 1)
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'scores' => $scores,
                'filtered_leads' => $results->sum('count'),
                'total_leads' => DB::table('leads_score')->count(),
                'db_list' => DB::table('leads_score')
                    ->select('db')
                    ->distinct()
                    ->pluck('db')
                    ->toArray()
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getScoreDistribution: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Errore nel recupero della distribuzione dei punteggi'
            ], 500);
        }
    }
}
