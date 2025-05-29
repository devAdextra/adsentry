<?php

namespace App\Jobs;

use App\Models\Download;
use App\Services\LeadScoringService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class GenerateDownloadFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $download;

    public function __construct(Download $download)
    {
        $this->download = $download;
    }

    public function handle(LeadScoringService $scoringService)
    {
        try {
            Log::info('Inizio generazione file', [
                'download_id' => $this->download->id,
                'filters' => $this->download->filters,
                'selectedDb' => $this->download->selectedDb
            ]);

            // Recupera i leads filtrati usando il service
            $leads = $scoringService->getLeadsByScore(
                $this->download->filters,
                $this->download->selectedDb
            );

            // Log dettagliato dei risultati
            Log::info('Risultati query', [
                'count' => count($leads),
                'first_few' => array_slice($leads, 0, 5),
                'query' => DB::getQueryLog()
            ]);

            // Verifica che ci siano risultati
            if (empty($leads)) {
                Log::warning('Nessun risultato trovato per i filtri specificati', [
                    'filters' => $this->download->filters,
                    'selectedDb' => $this->download->selectedDb
                ]);
            }

            // Usa il path già salvato nel database
            $path = storage_path('app/' . $this->download->path);
            
            // Assicurati che la directory esista
            if (!is_dir(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }

            // Apri il file in modalità scrittura
            $handle = fopen($path, 'w');
            if ($handle === false) {
                throw new \Exception("Impossibile creare il file: {$path}");
            }

            // Scrivi l'intestazione
            fputcsv($handle, ['Email', 'Database']);
            
            // Scrivi i dati
            $written = 0;
            foreach ($leads as $lead) {
                // Verifica che le proprietà esistano
                if (!isset($lead->email) || !isset($lead->db)) {
                    Log::warning('Record con proprietà mancanti', [
                        'record' => $lead
                    ]);
                    continue;
                }

                // I risultati sono oggetti stdClass, accediamo alle proprietà con ->
                fputcsv($handle, [
                    $lead->email,
                    $lead->db
                ]);
                $written++;
            }
            
            // Chiudi il file
            fclose($handle);

            Log::info('File scritto', [
                'path' => $path,
                'records_written' => $written,
                'total_records' => count($leads)
            ]);

            // Verifica che il file sia stato creato correttamente
            if (!file_exists($path)) {
                throw new \Exception("Il file non è stato creato correttamente: {$path}");
            }

            // Verifica la dimensione del file
            $fileSize = filesize($path);
            Log::info('Dimensione file', [
                'path' => $path,
                'size' => $fileSize
            ]);

            // Aggiorna il record del download
            $this->download->update([
                'status' => 'completed',
                'total_records' => $written
            ]);

            Log::info('File generato con successo', [
                'download_id' => $this->download->id,
                'path' => $path,
                'total_records' => $written,
                'file_size' => $fileSize
            ]);

        } catch (\Exception $e) {
            Log::error('Errore nella generazione del file: ' . $e->getMessage(), [
                'download_id' => $this->download->id,
                'filters' => $this->download->filters,
                'selectedDb' => $this->download->selectedDb,
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->download->update([
                'status' => 'failed'
            ]);
            
            throw $e;
        }
    }
}
