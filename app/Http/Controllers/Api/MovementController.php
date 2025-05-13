<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movement;
use Illuminate\Http\Request;

class MovementController extends Controller
{
    /**
     * 1) Fornisce tutti i movimenti, con filtri (date, macro, micro, nano, extra).
     *    Restituisce un JSON con la lista di movimenti.
     */
    public function index(Request $request)
    {
        // Estraiamo i filtri dal query string
        $startDate = $request->input('start', '2023-01-01');
        $endDate = $request->input('end', '2025-12-31');
        $macro = $request->input('macro');
        $micro = $request->input('micro');
        $nano = $request->input('nano');
        $extra = $request->input('extra');

        // Costruiamo la query
        $query = Movement::query();

        // Filtra per date
        $query->whereBetween('timeActionOpen', [$startDate, $endDate]);

        // Filtra macro
        if (!empty($macro) && $macro !== 'Tutti') {
            $query->where('macro', $macro);
        }
        // Filtra micro
        if (!empty($micro) && $micro !== 'Tutti') {
            $query->where('micro', $micro);
        }
        // Filtra nano
        if (!empty($nano) && $nano !== 'Tutti') {
            $query->where('nano', $nano);
        }
        // Filtra extra
        if (!empty($extra) && $extra !== 'Tutti') {
            $query->where('extra', $extra);
        }

        // Recuperiamo tutti i movimenti (eventualmente potresti fare ->paginate(50) se vuoi la paginazione)
        $movements = $query->get();

        return response()->json([
            'success' => true,
            'filters' => [
                'start' => $startDate,
                'end' => $endDate,
                'macro' => $macro,
                'micro' => $micro,
                'nano' => $nano,
                'extra' => $extra,
            ],
            'data' => $movements
        ]);
    }

    /**
     * 2) Fornisce tutti i movimenti di un singolo lead, con eventuali filtri (date, macro, micro, nano, extra).
     */
    public function leadMovements(Request $request, $leadId)
    {
        // Stessi filtri per date, macro, micro, nano, extra
        $startDate = $request->input('start', '2023-01-01');
        $endDate = $request->input('end', '2025-12-31');
        $macro = $request->input('macro');
        $micro = $request->input('micro');
        $nano = $request->input('nano');
        $extra = $request->input('extra');

        $query = Movement::where('user_id', $leadId)
            ->whereBetween('timeActionOpen', [$startDate, $endDate]);

        if (!empty($macro) && $macro !== 'Tutti') {
            $query->where('macro', $macro);
        }
        if (!empty($micro) && $micro !== 'Tutti') {
            $query->where('micro', $micro);
        }
        if (!empty($nano) && $nano !== 'Tutti') {
            $query->where('nano', $nano);
        }
        if (!empty($extra) && $extra !== 'Tutti') {
            $query->where('extra', $extra);
        }

        $movements = $query->get();

        return response()->json([
            'success' => true,
            'leadId' => $leadId,
            'filters' => [
                'start' => $startDate,
                'end' => $endDate,
                'macro' => $macro,
                'micro' => $micro,
                'nano' => $nano,
                'extra' => $extra,
            ],
            'movements' => $movements
        ]);
    }

    /**
     * 3) Restituisce il totale dei movimenti (numero di record in movements),
     *    filtrati per date, macro, micro, nano, extra.
     */
    public function totalMovements(Request $request)
    {
        $startDate = $request->input('start', '2023-01-01');
        $endDate = $request->input('end', '2025-12-31');
        $macro = $request->input('macro');
        $micro = $request->input('micro');
        $nano = $request->input('nano');
        $extra = $request->input('extra');

        $query = Movement::whereBetween('timeActionOpen', [$startDate, $endDate]);

        if (!empty($macro) && $macro !== 'Tutti') {
            $query->where('macro', $macro);
        }
        if (!empty($micro) && $micro !== 'Tutti') {
            $query->where('micro', $micro);
        }
        if (!empty($nano) && $nano !== 'Tutti') {
            $query->where('nano', $nano);
        }
        if (!empty($extra) && $extra !== 'Tutti') {
            $query->where('extra', $extra);
        }

        $count = $query->count();

        return response()->json([
            'success' => true,
            'totalMovements' => $count
        ]);
    }

    /**
     * 4) Restituisce il totale dei lead unici nei movimenti, con filtri (date, macro, micro, nano, extra).
     */
    public function uniqueLeads(Request $request)
    {
        try {
            $query = Movement::query();

            // Applica i filtri
            foreach (['macro', 'micro', 'nano', 'extra'] as $field) {
                $value = $request->input($field);
                if (!empty($value) && $value !== 'Tutti') {
                    $query->where($field, trim(explode('(', $value)[0]));
                }
            }

            // Conta i lead unici
            $countDistinct = $query->distinct('user_id')->count('user_id');

            return response()->json([
                'success' => true,
                'uniqueLeads' => $countDistinct
            ]);
        } catch (\Exception $e) {
            \Log::error('uniqueLeads error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function monthlyStats(Request $request)
    {
        try {
            $query = Movement::query();
            
            // Applica i filtri
            foreach (['macro', 'micro', 'nano', 'extra'] as $field) {
                $value = $request->input($field);
                if (!empty($value) && $value !== 'Tutti') {
                    $query->where($field, trim(explode('(', $value)[0]));
                }
            }

            // Ottieni i dati degli ultimi 12 mesi, ordinati per data
            $endDate = now();
            $startDate = now()->subMonths(11)->startOfMonth();

            $stats = $query->whereBetween('timeActionOpen', [$startDate, $endDate])
                ->selectRaw('DATE_FORMAT(timeActionOpen, "%Y-%m") as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();

            // Prepara array di tutti i mesi possibili
            $allMonths = [];
            $allCounts = [];
            
            // Genera tutti i mesi nel range
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $monthKey = $currentDate->format('Y-m');
                $monthLabel = $currentDate->format('M Y');
                
                $allMonths[] = $monthLabel;
                // Trova il conteggio per questo mese, o usa 0 se non presente
                $allCounts[] = (int)($stats->firstWhere('month', $monthKey)?->count ?? 0);
                
                $currentDate->addMonth();
            }

            return response()->json([
                'success' => true,
                'months' => $allMonths,
                'counts' => $allCounts
            ]);
        } catch (\Exception $e) {
            \Log::error('monthlyStats error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
