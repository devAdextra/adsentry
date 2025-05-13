<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Movement;

class DashboardController extends Controller
{
    public function index()
    {
        // Esempio di recupero di dati per la dashboard:
        $totalLeads = Lead::count();
        $totalMovements = Movement::count();

        // Esempio di recupero di ultimi movimenti:
        $recentMovements = Movement::with('lead')->latest('id')->take(10)->get();

        return view('dashboard.index', compact('totalLeads', 'totalMovements', 'recentMovements'));
    }

    /**
     * Esempio di generazione di un file di download.
     */
    public function downloadAllMovements()
    {
        // Recupera tutti i lead che hanno movimenti
        // (se vuoi anche i lead senza movimenti, rimuovi "has('movements')")
        $leads = Lead::has('movements')->with('movements')->get();

        // Nome del file
        $filename = 'movements_' . date('Ymd_His') . '.csv';
        $handle = fopen($filename, 'w');

        // Intestazione (opzionale, toglila se non ti serve)
        fputcsv($handle, ['Mail', 'DB']);

        foreach ($leads as $lead) {
            // Raccogli tutti i valori 'db' dai movimenti di questo lead
            // Li uniamo in una singola stringa, separati da virgole
            $dbValues = $lead->movements->pluck('db')->unique()->implode(', ');

            fputcsv($handle, [
                $lead->email,   // Email del lead
                $dbValues       // Tutti i valori 'db' uniti
            ]);
        }

        fclose($handle);

        // Restituisci il file come download e cancellalo dopo l'invio
        return response()->download($filename)->deleteFileAfterSend(true);
    }

    /**
     * Mostra la lista dei download precedentemente generati (se li salvi su disco o DB).
     */
    public function listDownloads()
    {
        // Se salvi i file in storage/app/downloads, potresti fare una scansione della cartella
        // o leggere da una tabella "downloads" se tieni traccia dei file generati.
        // Per ora, immaginiamo di avere un array fittizio:
        $downloads = [
            ['filename' => 'movements_20250325_101010.csv', 'created_at' => now()],
            // ...
        ];

        return view('dashboard.downloads', compact('downloads'));
    }
}
