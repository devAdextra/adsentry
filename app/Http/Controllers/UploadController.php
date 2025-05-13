<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;

class UploadController extends Controller
{
    public function showUploadForm()
    {
        return view('upload.index');
    }

    public function handleUpload(Request $request)
    {
        // Validazione basica
        $request->validate([
            'upload_file' => 'required|file|mimes:csv,txt,xlsx'
            // Cambia i mime type in base ai formati accettati
        ]);

        // Salva il file in storage/app/uploads
        $path = $request->file('upload_file')->store('uploads');

        // Qui puoi processare il file (ad esempio CSV) per inserire i lead o i movimenti
        // Esempio di elaborazione CSV (semplificato)
        /*
        $file = fopen(storage_path('app/' . $path), 'r');
        while (($data = fgetcsv($file, 1000, ',')) !== false) {
            // $data è un array con le colonne
            // Esempio: $data[0] => email, $data[1] => nome, etc.
            Lead::updateOrCreate([
                'email' => $data[0]
            ], [
                'nome' => $data[1],
                'cognome' => $data[2],
                'citta' => $data[3],
                'cap' => $data[4]
            ]);
        }
        fclose($file);
        */

        return back()->with('success', 'File caricato e processato correttamente!');
    }
}
