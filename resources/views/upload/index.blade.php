@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xl-9 mx-auto">
            <h6 class="mb-0 text-uppercase">Carica File</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    <form id="upload-form" action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div id="drop-area" class="border border-primary rounded-3 p-5 text-center bg-light position-relative" style="cursor:pointer;">
                            <input type="file" name="file" id="fileElem" class="d-none" onchange="handleFiles(this.files)">
                            <div id="drop-message">
                                <svg width="80" height="80" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M40 60V40" stroke="#008cff" stroke-width="3" stroke-linecap="round"/>
                                    <path d="M32 48l8-8 8 8" stroke="#008cff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M60 48a20 20 0 10-40 0" stroke="#008cff" stroke-width="3" stroke-linecap="round"/>
                                </svg>
                                <div class="mt-3 text-primary fw-bold">Trascina qui il file oppure <span class="text-decoration-underline" style="cursor:pointer;" onclick="document.getElementById('fileElem').click()">seleziona</span></div>
                            </div>
                            <div id="file-preview" class="mt-3"></div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3" id="upload-btn" disabled>Carica</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-xl-9 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4>File Caricati</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nome File</th>
                                    <th>Database</th>
                                    <th>Dimensione</th>
                                    <th>Data Caricamento</th>
                                    <th>Stato</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($uploads as $upload)
                                <tr>
                                    <td>{{ $upload->filename }}</td>
                                    <td>{{ $upload->db }}</td>
                                    <td>{{ number_format($upload->file_size / 1024, 2) }} KB</td>
                                    <td>{{ $upload->upload_at ? $upload->upload_at->format('d/m/Y H:i') : '-' }}</td>
                                    <td>
                                        @if($upload->processed_at)
                                            <span class="badge bg-success">Processato</span>
                                        @else
                                            <span class="badge bg-warning">In Attesa</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('upload.download', $upload->id) }}" class="btn btn-sm btn-info">
                                            <i class="material-icons-outlined">download</i>
                                        </a>
                                        <form action="{{ route('upload.destroy', $upload->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questo file?')">
                                                <i class="material-icons-outlined">delete</i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Nessun file caricato</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
#drop-area {
    transition: border-color 0.2s, background 0.2s;
}
#drop-area.dragover {
    border-color: #157EFB;
    background: #eaf4ff;
}
</style>
@endpush

@push('scripts')
<script>
const dropArea = document.getElementById('drop-area');
const fileElem = document.getElementById('fileElem');
const filePreview = document.getElementById('file-preview');
const uploadBtn = document.getElementById('upload-btn');

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, preventDefaults, false);
});

dropArea.addEventListener('dragover', () => dropArea.classList.add('dragover'));
dropArea.addEventListener('dragleave', () => dropArea.classList.remove('dragover'));
dropArea.addEventListener('drop', (e) => {
    dropArea.classList.remove('dragover');
    const dt = e.dataTransfer;
    const files = dt.files;
    fileElem.files = files;
    handleFiles(files);
});

dropArea.addEventListener('click', () => fileElem.click());

function handleFiles(files) {
    if (!files.length) return;
    filePreview.innerHTML = '';
    const file = files[0];
    const icon = '<svg width="40" height="40" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 30V20" stroke="#008cff" stroke-width="2" stroke-linecap="round"/><path d="M16 24l4-4 4 4" stroke="#008cff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M30 24a10 10 0 10-20 0" stroke="#008cff" stroke-width="2" stroke-linecap="round"/></svg>';
    filePreview.innerHTML = icon + '<div class="mt-2">' + file.name + ' (' + Math.round(file.size/1024) + ' KB)</div>';
    uploadBtn.disabled = false;
}
</script>
@endpush
@endsection