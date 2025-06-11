<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Upload;
use Illuminate\Support\Facades\File;
use App\Models\Lead;
use App\Models\Movement;

class ProcessManualUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $filename;
    public function __construct($filename)
    {
        $this->filename = $filename;
        Log::debug('[UPLOAD JOB] Ricevuto filename: ' . $this->filename);
    }

    public function handle()
    {
        $filepath = storage_path('app/uploads/' . $this->filename);
        Log::info('[UPLOAD JOB] Inizio processing file: ' . $filepath);
        if (!File::exists($filepath)) {
            Log::error('[UPLOAD JOB] File non trovato: ' . $filepath);
            return;
        }
        $size = File::size($filepath);
        $mime = File::mimeType($filepath);
        $filename = $this->filename;
        $uploadAt = now();
        // Salva il record nella tabella uploads (se non esiste già)
        $upload = Upload::firstOrCreate([
            'filename' => $filename,
            'file_path' => 'uploads/' . $filename
        ], [
            'file_size' => $size,
            'mime_type' => $mime,
            'upload_at' => $uploadAt,
        ]);
        $upload->save();
        $rowCount = 0;
        $leadCreated = 0;
        $movementCreated = 0;
        $errorCount = 0;
        $totalRows = 0;
        // Conta le righe totali (escluso header)
        if (($handle = fopen($filepath, 'r')) !== false) {
            while (fgets($handle) !== false) {
                $totalRows++;
            }
            fclose($handle);
        }
        $totalRows = max(0, $totalRows - 1);
        // Processa il CSV
        if (($handle = fopen($filepath, 'r')) !== false) {
            $header = fgetcsv($handle, 0, ',');
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                $rowCount++;
                // Validazione riga
                if (count($row) < count($header)) {
                    $errorCount++;
                    continue;
                }
                $data = array_combine($header, $row);
                $email = trim($data['email'] ?? '');
                if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errorCount++;
                    continue;
                }
                // Lead: se non esiste, crea
                $lead = Lead::firstOrCreate(
                    ['email' => $email],
                    ['nome' => '', 'cognome' => '', 'cellulare' => '', 'cap' => '']
                );
                if (!$lead || !$lead->id) {
                    $errorCount++;
                    continue;
                }
                if ($lead->wasRecentlyCreated) {
                    $leadCreated++;
                }
                // Movements: category_name -> action, macro, micro, nano, extra
                $cat = $data['category_name'] ?? '';
                $parts = array_map('trim', array_pad(explode('-', $cat), 6, null));
                $action = $parts[0];
                $macro = $parts[1];
                $micro = $parts[2];
                $nano = $parts[3];
                $extra = $parts[4];
                $dbval = $parts[5];
                $last_open = $data['last_open'] ?? null;
                $last_click = $data['last_click'] ?? null;
                try {
                    Movement::create([
                        'user_id' => $lead->id,
                        'action' => $action,
                        'macro' => $macro,
                        'micro' => $micro,
                        'nano' => $nano,
                        'extra' => $extra,
                        'db' => $dbval,
                        'timeActionOpen' => $last_open ?: null,
                        'timeActionClick' => $last_click ?: null,
                        'timeCharge' => now(),
                    ]);
                    $movementCreated++;
                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error('[UPLOAD JOB] Errore inserimento movimento: ' . $e->getMessage());
                }
                // Aggiorna progresso ogni 10 righe o alla fine
                if ($rowCount % 10 === 0 || $rowCount === $totalRows) {
                    $upload->progress = $totalRows > 0 ? ($rowCount / $totalRows) * 100 : 100;
                    $upload->lead_processed = $leadCreated;
                    $upload->movements_created = $movementCreated;
                    $upload->errors = $errorCount;
                    $upload->save();
                }
            }
            fclose($handle);
        }
        // Aggiorna processed_at e stato finale
        $upload->processed_at = now();
        $upload->status = 'completato';
        $upload->progress = 100;
        $upload->lead_processed = $leadCreated;
        $upload->movements_created = $movementCreated;
        $upload->errors = $errorCount;
        $upload->save();
        // Rinomina il file processato
        $newFilename = 'processed_' . $filename;
        $newPath = 'uploads/' . $newFilename;
        if (File::exists($filepath)) {
            File::move($filepath, storage_path('app/' . $newPath));
        }
        $upload->filename = $newFilename;
        $upload->file_path = $newPath;
        $upload->save();
        Log::info("[UPLOAD JOB] Fine processing $filename: $rowCount righe lette, $leadCreated nuovi lead, $movementCreated movements inseriti, $errorCount errori.");
    }
} 