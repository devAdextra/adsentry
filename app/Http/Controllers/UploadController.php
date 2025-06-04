<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Jobs\ProcessManualUpload;

class UploadController extends Controller
{
    public function index()
    {
        $uploads = Upload::latest()->get();
        return view('upload.index', compact('uploads'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file'
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('uploads', $filename, 'public');

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
}
