@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body d-flex align-items-center">
                <div class="d-flex align-items-center gap-4">
                    <div class="rounded-circle overflow-hidden">
                        <img src="{{ asset('assets/images/avatars/01.png') }}" width="48" height="48" class="rounded-circle" alt="">
                    </div>
                    <div>
                        <h6 class="mb-0">Downloads</h6>
                        <p class="mb-0">Gestione dei download</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="mb-0">Lista Download</h5>
                <div class="ms-auto">
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control form-control-sm" placeholder="Cerca..." id="searchInput">
                        <select class="form-select form-select-sm" id="statusFilter">
                            <option value="" {{ request('status') == '' ? 'selected' : '' }}>Tutti gli stati</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>In elaborazione</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completato</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Fallito</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nome File</th>
                                <th>Stato</th>
                                <th>Filtri</th>
                                <th>Utente</th>
                                <th>Data Creazione</th>
                                <th>DB Selezionato</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($downloads as $download)
                            <tr>
                                <td>{{ $download->original_filename }}</td>
                                <td>
                                    <span class="badge {{ $download->status_badge }}">
                                        {{ ucfirst($download->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($download->filters)
                                        @foreach($download->filters as $key => $value)
                                            <span class="badge bg-light-primary">{{ $key }}: {{ $value }}</span>
                                        @endforeach
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $download->user_id ?? 'Sistema' }}</td>
                                <td>{{ $download->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $download->selectedDb ?? '-' }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                            <i class="material-icons-outlined">more_vert</i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if($download->status === 'completed')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('downloads.download', $download) }}">
                                                    <i class="material-icons-outlined">download</i> Scarica
                                                </a>
                                            </li>
                                            @endif
                                            <li>
                                                <form action="{{ route('downloads.destroy', $download) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="material-icons-outlined">delete</i> Elimina
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        Showing {{ $downloads->firstItem() }} to {{ $downloads->lastItem() }} of {{ $downloads->total() }} entries
                    </div>
                    {{ $downloads->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Gestione ricerca e filtri
    let timer;
    $('#searchInput, #statusFilter').on('input change', function() {
        clearTimeout(timer);
        timer = setTimeout(function() {
            const search = $('#searchInput').val();
            const status = $('#statusFilter').val();
            window.location.href = `{{ route('downloads.index') }}?search=${search}&status=${status}`;
        }, 500);
    });
});
</script>
@endpush 