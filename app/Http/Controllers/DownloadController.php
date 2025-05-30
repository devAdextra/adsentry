<?php

namespace App\Http\Controllers;

use App\Models\Download;
use Illuminate\Http\Request;
use App\Jobs\GenerateDownloadFileJob;

class DownloadController extends Controller
{
    public function index(Request $request)
    {
        $downloads = Download::query()
            ->when($request->search, function($query, $search) {
                $query->where('filename', 'like', "%{$search}%")
                    ->orWhere('user', 'like', "%{$search}%");
            })
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('downloads.index', compact('downloads'));
    }

    public function generate(Request $request)
    {
        // Validazione input
        $request->validate([
            'selectedDb' => 'required|string',
            'filters' => 'required|array',
        ]);

        $filename = 'export_' . now()->format('Ymd_His') . '.csv';

        // Crea una riga download (stato: processing)
        $download = Download::create([
            'filename' => $filename,
            'original_filename' => $filename,
            'path' => $filename,
            'filters' => $request->filters,
            'selectedDb' => $request->selectedDb,
            'user' => auth()->id() ?? null,
            'status' => 'processing',
            'expires_at' => now()->addDays(7), // Il file scadrà dopo 7 giorni
        ]);

        // Avvia un job per la generazione del file (opzionale, consigliato)
        dispatch(new GenerateDownloadFileJob($download));

        return response()->json(['success' => true]);
    }

    public function destroy(Download $download)
    {
        // Elimina il file fisico dalla cartella downloads
        $filePath = storage_path('app/downloads/' . $download->path);
        if ($download->path && file_exists($filePath)) {
            unlink($filePath);
        }
        
        $download->delete();
        
        return redirect()->route('downloads.index')
            ->with('success', 'Download eliminato con successo.');
    }

    public function download(Download $download)
    {
        if ($download->status !== 'completed' || !$download->path) {
            abort(404);
        }
        // Cerca sempre nella sottocartella downloads/
        $filePath = storage_path('app/downloads/' . $download->path);
        if (!file_exists($filePath)) {
            abort(404, 'File non trovato');
        }
        return response()->download($filePath);
    }
} 