<?php

namespace App\Services\Scoring;

class ScoreRanges
{
    public const RANGES = [
        9 => ['min' => 101, 'description' => 'Excellent'],
        8 => ['min' => 76,  'description' => 'Very Good'],
        7 => ['min' => 51,  'description' => 'Good'],
        6 => ['min' => 36,  'description' => 'Above Average'],
        5 => ['min' => 21,  'description' => 'Average'],
        4 => ['min' => 11,  'description' => 'Below Average'],
        3 => ['min' => 6,   'description' => 'Poor'],
        2 => ['min' => 1,   'description' => 'Very Poor'],
        1 => ['min' => 0,   'description' => 'Inactive']
    ];

    public static function getScoreForValue(int $value): int
    {
        foreach (self::RANGES as $score => $range) {
            if ($value >= $range['min']) {
                return $score;
            }
        }
        return 1;
    }
} 