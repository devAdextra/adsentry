<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Jobs\ProcessManualUpload;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    public function index()
    {
        // Recupera i nomi dei file già presenti nel database
        $uploadedFilenames = Upload::pluck('filename')->toArray();

        // Filtra i file fisici escludendo quelli già nel database
        $existingFiles = collect(Storage::files('uploads'))
            ->filter(function ($file) use ($uploadedFilenames) {
                $filename = basename($file);
                return strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'csv'
                    && !in_array($filename, $uploadedFilenames);
            })
            ->map(function ($file) {
                $filename = basename($file);
                return [
                    'filename' => $filename,
                    'file_path' => $file,
                    'file_size' => Storage::size($file),
                    'mime_type' => Storage::mimeType($file),
                    'upload_at' => date('Y-m-d H:i:s', Storage::lastModified($file)),
                    'status' => 'da processare',
                    'is_existing' => true
                ];
            });

        // Recupera i file già caricati dal database
        $uploadedFiles = Upload::all()->map(function ($upload) {
            return [
                'filename' => $upload->filename,
                'file_path' => $upload->file_path,
                'file_size' => $upload->file_size,
                'mime_type' => $upload->mime_type,
                'upload_at' => $upload->upload_at,
                'status' => $upload->processed_at ? 'completato' : 'da processare',
                'is_existing' => false,
                'progress' => $upload->progress ?? 0,
                'lead_processed' => $upload->lead_processed ?? 0,
                'movements_created' => $upload->movements_created ?? 0
            ];
        });

        // Unisci le due collezioni
        $allFiles = $existingFiles->concat($uploadedFiles);

        return view('upload.index', compact('allFiles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file'
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('uploads', $filename);

        Upload::create([
            'filename' => $filename,
            'db' => 'default', // Questo può essere modificato in base alle tue esigenze
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'upload_at' => now(),
        ]);

        return redirect()->route('upload.index')->with('success', 'File caricato con successo');
    }

    public function download(Upload $upload)
    {
        return Storage::disk('public')->download($upload->file_path, $upload->filename);
    }

    public function destroy(Upload $upload)
    {
        Storage::disk('public')->delete($upload->file_path);
        $upload->delete();
        return redirect()->route('upload.index')->with('success', 'File eliminato con successo');
    }

    /**
     * Mostra i file caricati manualmente e permette di processarli.
     */
    public function manualUploads()
    {
        $dir = storage_path('app/uploads');
        $files = File::files($dir);
        return view('upload.manual', compact('files'));
    }

    /**
     * Lancia il job di processing per un file caricato manualmente.
     */
    public function processManualUpload(Request $request)
    {
        $request->validate([
            'filename' => 'required|string',
            'db' => 'required|string',
        ]);
        $filepath = storage_path('app/uploads/' . $request->filename);
        if (!File::exists($filepath)) {
            return back()->with('error', 'File non trovato');
        }
        // Lancia il job in background, passando anche il db
        ProcessManualUpload::dispatch($request->filename, $request->db);
        return back()->with('success', 'Processing avviato per ' . $request->filename);
    }

    public function validateCsv($filepath)
    {
        if (($handle = fopen($filepath, 'r')) !== false) {
            // Leggi l'header
            $header = fgetcsv($handle, 0, ',');
            $requiredHeaders = ['email', 'category_name', 'last_open', 'last_click'];
            
            // Verifica header obbligatori
            $missingHeaders = array_diff($requiredHeaders, $header);
            if (!empty($missingHeaders)) {
                fclose($handle);
                return [
                    'valid' => false,
                    'message' => 'Header mancanti: ' . implode(', ', $missingHeaders)
                ];
            }

            // Leggi la prima riga di dati
            $firstRow = fgetcsv($handle, 0, ',');
            if ($firstRow) {
                $data = array_combine($header, $firstRow);
                
                // Verifica formato email
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    fclose($handle);
                    return [
                        'valid' => false,
                        'message' => 'Formato email non valido nella prima riga'
                    ];
                }

                // Verifica formato category_name
                if (!str_contains($data['category_name'], '-')) {
                    fclose($handle);
                    return [
                        'valid' => false,
                        'message' => 'Formato category_name non valido nella prima riga'
                    ];
                }
            }

            fclose($handle);
            return ['valid' => true];
        }

        return [
            'valid' => false,
            'message' => 'Impossibile leggere il file'
        ];
    }

    public function processFile(Request $request)
    {
        $filename = $request->filename;
        $filepath = storage_path('app/uploads/' . $filename);

        // Valida il file
        $validation = $this->validateCsv($filepath);
        if (!$validation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $validation['message']
            ]);
        }

        // Crea o aggiorna il record Upload
        $upload = Upload::firstOrCreate(
            ['filename' => $filename],
            [
                'file_path' => 'uploads/' . $filename,
                'file_size' => File::size($filepath),
                'mime_type' => File::mimeType($filepath),
                'upload_at' => now(),
                'status' => 'in elaborazione',
                'progress' => 0
            ]
        );

        // Avvia il job di processamento
        ProcessManualUpload::dispatch($filename);

        return response()->json([
            'success' => true,
            'message' => 'File in elaborazione'
        ]);
    }

    public function getProgress($filename)
    {
        $upload = Upload::where('filename', $filename)->first();
        
        if (!$upload) {
            return response()->json([
                'success' => false,
                'message' => 'File non trovato'
            ]);
        }

        return response()->json([
            'success' => true,
            'progress' => $upload->progress ?? 0,
            'status' => $upload->status,
            'lead_processed' => $upload->lead_processed ?? 0,
            'movements_created' => $upload->movements_created ?? 0
        ]);
    }
}
