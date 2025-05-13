<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FilterController extends Controller
{
    private $cacheTime = 3600; // 1 ora

    public function getFilterOptions(Request $request)
    {
        try {
            $column = $request->input('column');
            $filters = $request->except(['column']);
            
            // Rimuovi la cache e procedi direttamente con la query
            $query = Movement::query();
            
            // Applica i filtri attivi
            foreach (['macro', 'micro', 'nano', 'extra'] as $field) {
                if (!empty($filters[$field]) && $filters[$field] !== 'Tutti') {
                    $query->where($field, $filters[$field]);
                }
            }
            
            // Ottieni i valori distinti con il conteggio
            $results = $query->select($column, DB::raw('COUNT(*) as count'))
                            ->whereNotNull($column)
                            ->where($column, '!=', '')
                            ->groupBy($column)
                            ->orderBy($column, 'asc')
                            ->get();

            \Log::info('Query results:', [
                'column' => $column,
                'count' => $results->count(),
                'results' => $results
            ]);

            return response()->json([
                'success' => true,
                'results' => $results
            ]);
        } catch (\Exception $e) {
            \Log::error('Errore in getFilterOptions', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Errore durante il recupero delle opzioni'
            ], 500);
        }
    }
} 