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
                'first_few' => array_slice($leads, 0, 5)
            ]);

            // Verifica che ci siano risultati
            if (empty($leads)) {
                Log::warning('Nessun risultato trovato per i filtri specificati', [
                    'filters' => $this->download->filters,
                    'selectedDb' => $this->download->selectedDb
                ]);
            }

            // Assicurati che la directory esista
            Storage::disk('downloads')->makeDirectory('');

            // Genera il file CSV
            $output = fopen('php://temp', 'r+');
            
            // Intestazioni CSV
            fputcsv($output, ['Email', 'DB']);

            // Dati
            foreach ($leads as $lead) {
                fputcsv($output, [
                    $lead->email,
                    $lead->db
                ]);
            }

            rewind($output);
            $csv = stream_get_contents($output);
            fclose($output);

            // Salva il file
            Storage::disk('downloads')->put($this->download->filename, $csv);

            // Aggiorna lo stato del download
            $this->download->update([
                'status' => 'completed',
                'total_records' => count($leads)
            ]);

            Log::info('File generato con successo', [
                'download_id' => $this->download->id,
                'filename' => $this->download->filename,
                'total_records' => count($leads)
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