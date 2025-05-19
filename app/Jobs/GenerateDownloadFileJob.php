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
            // Recupera i leads filtrati usando il service
            $leads = $scoringService->getLeadsByScore(
                $this->download->filters,
                $this->download->selectedDb
            );

            // Genera il file CSV
            $filename = 'downloads/export_' . now()->format('Ymd_His') . '_' . uniqid() . '.csv';
            $path = storage_path('app/' . $filename);
            
            if (!is_dir(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }

            $handle = fopen($path, 'w');
            fputcsv($handle, ['Email', 'Database']);
            
            foreach ($leads as $lead) {
                fputcsv($handle, [$lead->email, $lead->db]);
            }
            
            fclose($handle);

            // Aggiorna il record del download
            $this->download->update([
                'filename' => $filename,
                'status' => 'completed'
            ]);

        } catch (\Exception $e) {
            \Log::error('Errore nella generazione del file: ' . $e->getMessage(), [
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
