<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Services\Scoring\ScoreRanges;
use Illuminate\Support\Facades\Log;

class LeadScoringService
{
    public function getLeadsByScore($filters, $selectedDb)
    {
        // Log dei parametri in ingresso
        Log::info('Parametri query', [
            'filters' => $filters,
            'selectedDb' => $selectedDb
        ]);

        $query = "WITH FilteredUsers AS (
            SELECT DISTINCT m.user_id
            FROM movements m
            USE INDEX (idx_filters)
            WHERE m.db = ?";
        
        $params = [$selectedDb];
        
        // Applica i filtri solo se non sono 'Tutti'
        foreach (['macro', 'micro', 'nano', 'extra'] as $field) {
            if (!empty($filters[$field]) && $filters[$field] !== 'Tutti') {
                $query .= " AND m.$field = ?";
                $params[] = $filters[$field];
            }
        }

        $query .= "),
        UserActions AS (
            SELECT 
                m.user_id,
                m.db,
                COUNT(DISTINCT CASE WHEN m.timeActionOpen IS NOT NULL THEN m.timeActionOpen END) as open_count,
                COUNT(DISTINCT CASE WHEN m.timeActionClick IS NOT NULL THEN m.timeActionClick END) as click_count
            FROM movements m
            USE INDEX (idx_filters)
            INNER JOIN FilteredUsers fu ON m.user_id = fu.user_id
            WHERE m.db = ?
            GROUP BY m.user_id, m.db
        ),
        ScoreCalculation AS (
            SELECT 
                ua.user_id,
                ua.db,
                CASE
                    WHEN (ua.open_count + (ua.click_count * 2)) = 0 THEN 1
                    WHEN (ua.open_count + (ua.click_count * 2)) <= 5 THEN 2
                    WHEN (ua.open_count + (ua.click_count * 2)) <= 10 THEN 3
                    WHEN (ua.open_count + (ua.click_count * 2)) <= 20 THEN 4
                    WHEN (ua.open_count + (ua.click_count * 2)) <= 35 THEN 5
                    WHEN (ua.open_count + (ua.click_count * 2)) <= 50 THEN 6
                    WHEN (ua.open_count + (ua.click_count * 2)) <= 75 THEN 7
                    WHEN (ua.open_count + (ua.click_count * 2)) <= 100 THEN 8
                    ELSE 9
                END as score_group
            FROM UserActions ua
        )
        SELECT 
            l.email,
            sc.db
        FROM ScoreCalculation sc
        INNER JOIN leads l ON sc.user_id = l.id
        WHERE sc.score_group = ?";

        // Aggiungi il selectedDb come parametro per la seconda query
        $params[] = $selectedDb;
        // Aggiungi il score come ultimo parametro
        $params[] = $filters['score'];

        // Log della query e dei parametri
        Log::info('Query eseguita', [
            'query' => $query,
            'params' => $params
        ]);

        $results = DB::select($query, $params);

        // Log dei risultati
        Log::info('Risultati query', [
            'count' => count($results),
            'first_few' => array_slice($results, 0, 5)
        ]);

        return $results;
    }
} 