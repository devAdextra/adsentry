<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Services\Scoring\ScoreRanges;

class LeadScoringService
{
    public function getLeadsByScore($filters, $selectedDb)
    {
        $query = "WITH FilteredUsers AS (
            SELECT DISTINCT m.user_id
            FROM movements m
            USE INDEX (idx_user_filters)
            WHERE m.db = ?";
        
        $params = [$selectedDb];
        
        // Applica i filtri
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
                MAX(m.db) as db,
                COUNT(DISTINCT CASE WHEN m.timeActionOpen IS NOT NULL THEN m.timeActionOpen END) as open_count,
                COUNT(DISTINCT CASE WHEN m.timeActionClick IS NOT NULL THEN m.timeActionClick END) as click_count
            FROM movements m
            INNER JOIN FilteredUsers fu ON m.user_id = fu.user_id
            GROUP BY m.user_id
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

        $params[] = $filters['score'];

        return DB::select($query, $params);
    }
}
