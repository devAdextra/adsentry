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
                $query->where('original_filename', 'like', "%{$search}%")
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

        // Crea una riga download (stato: processing)
        $download = Download::create([
            'original_filename' => 'export_' . now()->format('Ymd_His') . '.csv',
            'filters' => $request->filters,
            'selectedDb' => $request->selectedDb,
            'user_id' => auth()->id() ?? null,
            'status' => 'processing',
        ]);

        // Avvia un job per la generazione del file (opzionale, consigliato)
        dispatch(new GenerateDownloadFileJob($download));

        return response()->json(['success' => true]);
    }

    public function destroy(Download $download)
    {
        // Elimina il file fisico
        if (file_exists(storage_path('app/' . $download->path))) {
            unlink(storage_path('app/' . $download->path));
        }
        
        $download->delete();
        
        return redirect()->route('downloads.index')
            ->with('success', 'Download eliminato con successo.');
    }

    public function download(Download $download)
    {
        if ($download->status !== 'completed' || !$download->filename) {
            abort(404);
        }
        return response()->download(storage_path('app/' . $download->filename));
    }
} 