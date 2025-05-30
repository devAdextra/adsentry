<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateDownloadFileJob;
use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DownloadController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'selectedDb' => 'required|string',
            'filters' => 'required|array'
        ]);

        $download = Download::create([
            'selectedDb' => $request->selectedDb,
            'filters' => $request->filters,
            'status' => 'pending',
            'filename' => 'export_' . date('Ymd_His') . '.csv'
        ]);

        GenerateDownloadFileJob::dispatch($download);

        return response()->json([
            'message' => 'Download in corso',
            'download_id' => $download->id
        ]);
    }

    public function download($filename)
    {
        try {
            Log::info('Tentativo di download file', ['filename' => $filename]);
            
            // Verifica che il file esista
            if (!Storage::disk('downloads')->exists($filename)) {
                Log::error('File non trovato', ['filename' => $filename]);
                return response()->json(['error' => 'File non trovato'], 404);
            }

            // Recupera il record del download
            $download = Download::where('filename', $filename)->first();
            if (!$download) {
                Log::error('Record download non trovato', ['filename' => $filename]);
                return response()->json(['error' => 'Record download non trovato'], 404);
            }

            // Verifica che il file sia stato completato
            if ($download->status !== 'completed') {
                Log::warning('File non ancora pronto', [
                    'filename' => $filename,
                    'status' => $download->status
                ]);
                return response()->json(['error' => 'File non ancora pronto'], 400);
            }

            // Recupera il contenuto del file
            $file = Storage::disk('downloads')->get($filename);
            
            // Imposta gli headers per il download
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => strlen($file)
            ];

            Log::info('Download completato con successo', ['filename' => $filename]);
            
            // Restituisci il file come download
            return response($file, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Errore durante il download: ' . $e->getMessage(), [
                'filename' => $filename,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Errore durante il download'], 500);
        }
    }

    public function status($id)
    {
        $download = Download::findOrFail($id);
        return response()->json([
            'status' => $download->status,
            'total_records' => $download->total_records
        ]);
    }
} 