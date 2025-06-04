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
    public $db;

    /**
     * Create a new job instance.
     */
    public function __construct($filename, $db)
    {
        $this->filename = $filename;
        $this->db = $db;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $filepath = storage_path('app/uploads/' . $this->filename);
        \Log::info('[UPLOAD JOB] Inizio processing file: ' . $filepath);
        if (!File::exists($filepath)) {
            \Log::error('[UPLOAD JOB] File non trovato: ' . $filepath);
            return;
        }
        $size = File::size($filepath);
        $mime = File::mimeType($filepath);
        $filename = $this->filename;
        $db = $this->db;
        $uploadAt = now();
        // Salva il record nella tabella uploads (se non esiste già)
        $upload = \App\Models\Upload::firstOrCreate([
            'filename' => $filename,
            'file_path' => 'uploads/' . $filename
        ], [
            'db' => $db,
            'file_size' => $size,
            'mime_type' => $mime,
            'upload_at' => $uploadAt,
        ]);
        // Aggiorna sempre il db anche se già esiste
        $upload->db = $db;
        $upload->save();
        $rowCount = 0;
        $leadCreated = 0;
        $movementCreated = 0;
        // Processa il CSV
        if (($handle = fopen($filepath, 'r')) !== false) {
            $header = fgetcsv($handle, 0, ',');
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                $rowCount++;
                $data = array_combine($header, $row);
                $email = trim($data['email'] ?? '');
                if (!$email) {
                    \Log::warning("[UPLOAD JOB] Riga $rowCount: email vuota, salto.");
                    continue;
                }
                // Lead: se non esiste, crea
                $lead = Lead::firstOrCreate(
                    ['email' => $email],
                    ['nome' => '', 'cognome' => '', 'cellulare' => '', 'cap' => '']
                );
                if ($lead->wasRecentlyCreated) {
                    $leadCreated++;
                    \Log::info("[UPLOAD JOB] Riga $rowCount: Lead creato per $email (id: {$lead->id})");
                } else {
                    \Log::info("[UPLOAD JOB] Riga $rowCount: Lead già esistente per $email (id: {$lead->id})");
                }
                // Movements: category_name -> action, macro, micro, nano, extra, db
                $cat = $data['category_name'] ?? '';
                $parts = array_map('trim', array_pad(explode('-', $cat), 6, null));
                $action = $parts[0];
                $macro = $parts[1];
                $micro = $parts[2];
                $nano = $parts[3];
                $extra = $parts[4];
                $dbval = $parts[5] ?? $db; 
                \Log::info("[UPLOAD JOB] Riga $rowCount: category_name='$cat', estratti: action=$action, macro=$macro, micro=$micro, nano=$nano, extra=$extra, db=$dbval");
                $last_open = $data['last_open'] ?? null;
                $last_click = $data['last_click'] ?? null;
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
                \Log::info("[UPLOAD JOB] Riga $rowCount: Movement inserito per user_id {$lead->id} (action: $action, macro: $macro, micro: $micro, nano: $nano, extra: $extra, db: $dbval, open: $last_open, click: $last_click)");
            }
            fclose($handle);
        }
        // Aggiorna processed_at
        $upload->processed_at = now();
        $upload->save();
        \Log::info("[UPLOAD JOB] Fine processing $filename: $rowCount righe lette, $leadCreated nuovi lead, $movementCreated movements inseriti.");
    }
} 