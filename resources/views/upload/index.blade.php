@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Gestione File CSV</h4>
                </div>

                <div class="card-body">
                    <!-- Form di Upload -->
                    <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="file">Seleziona File CSV</label>
                                    <input type="file" class="form-control" id="file" name="file" accept=".csv" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="db">Database</label>
                                    <input type="text" class="form-control" id="db" name="db" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Carica File</button>
                    </form>

                    <!-- Tabella File -->
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nome File</th>
                                    <th>Dimensione</th>
                                    <th>Data Upload</th>
                                    <th>Stato</th>
                                    <th>Progresso</th>
                                    <th>Lead Processati</th>
                                    <th>Movimenti Creati</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allFiles as $file)
                                <tr>
                                    <td>{{ $file['filename'] }}</td>
                                    <td>{{ number_format($file['file_size'] / 1024, 2) }} KB</td>
                                    <td>{{ $file['upload_at'] }}</td>
                                    <td>
                                        <span class="badge bg-{{ $file['status'] === 'completato' ? 'success' : ($file['status'] === 'in elaborazione' ? 'warning' : 'secondary') }}">
                                            {{ $file['status'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($file['status'] === 'in elaborazione' || $file['status'] === 'completato')
                                            <div class="progress">
                                                <div class="progress-bar {{ $file['status'] === 'completato' ? 'bg-success' : '' }}" role="progressbar" 
                                                     style="width: {{ $file['status'] === 'completato' ? 100 : ($file['progress'] ?? 0) }}%"
                                                     data-filename="{{ $file['filename'] }}">
                                                    {{ $file['status'] === 'completato' ? '100.0' : number_format($file['progress'] ?? 0, 1) }}%
                                                </div>
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $file['lead_processed'] ?? 0 }}</td>
                                    <td>{{ $file['movements_created'] ?? 0 }}</td>
                                    <td>
                                        @if($file['status'] === 'da processare')
                                            <button class="btn btn-sm btn-primary process-file" 
                                                    data-filename="{{ $file['filename'] }}"
                                                    data-db="{{ $file['db'] ?? '' }}">
                                                Processa
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestione processamento file
    document.querySelectorAll('.process-file').forEach(button => {
        button.addEventListener('click', function() {
            const filename = this.dataset.filename;
            const db = this.dataset.db;
            
            // Disabilita il pulsante
            this.disabled = true;
            
            // Avvia il processamento
            fetch('{{ route("upload.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ filename, db })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Aggiorna lo stato
                    const row = this.closest('tr');
                    row.querySelector('td:nth-child(4) .badge').textContent = 'in elaborazione';
                    row.querySelector('td:nth-child(4) .badge').className = 'badge bg-warning';
                    
                    // Avvia il polling del progresso
                    startProgressPolling(filename);
                } else {
                    alert(data.message);
                    this.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Errore durante il processamento del file');
                this.disabled = false;
            });
        });
    });

    // Funzione per il polling del progresso
    function startProgressPolling(filename) {
        const progressBar = document.querySelector(`.progress-bar[data-filename="${filename}"]`);
        console.log('Progress bar element:', progressBar);
        if (!progressBar) {
            console.log('Progress bar not found for filename:', filename);
            return;
        }

        const pollInterval = setInterval(() => {
            fetch(`{{ url('upload/progress') }}/${filename}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Progress data:', data);
                    if (data.success) {
                        // Aggiorna la barra di progresso
                        progressBar.style.width = `${data.progress}%`;
                        progressBar.textContent = `${data.progress.toFixed(1)}%`;

                        // Aggiorna i contatori
                        const row = progressBar.closest('tr');
                        row.querySelector('td:nth-child(6)').textContent = data.lead_processed;
                        row.querySelector('td:nth-child(7)').textContent = data.movements_created;

                        // Se il processamento è completato
                        if (data.status === 'completato') {
                            clearInterval(pollInterval);
                            row.querySelector('td:nth-child(4) .badge').textContent = 'completato';
                            row.querySelector('td:nth-child(4) .badge').className = 'badge bg-success';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    clearInterval(pollInterval);
                });
        }, 1000);
    }
});
</script>
@endpush
@endsection