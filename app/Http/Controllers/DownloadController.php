<?php

namespace App\Http\Controllers;

use App\Models\Download;
use Illuminate\Http\Request;

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
} 