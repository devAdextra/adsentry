@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xl-9 mx-auto">
            <h6 class="mb-0 text-uppercase">File caricati manualmente</h6>
            <hr>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="card">
                <div class="card-body">
                    @if(count($files))
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nome file</th>
                                <th>Dimensione</th>
                                <th>Ultima modifica</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($files as $file)
                            <tr>
                                <td>{{ $file->getFilename() }}</td>
                                <td>{{ number_format($file->getSize() / 1024 / 1024, 2) }} MB</td>
                                <td>{{ date('d/m/Y H:i', $file->getMTime()) }}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="openDbModal('{{ $file->getFilename() }}')">Processa</button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @else
                        <div class="text-center">Nessun file trovato nella cartella uploads.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bootstrap per scelta DB -->
<div class="modal fade" id="dbModal" tabindex="-1" aria-labelledby="dbModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="dbForm" action="{{ route('upload.manual.process') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="dbModalLabel">Seleziona Database</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="filename" id="modalFilename">
          <div class="mb-3">
            <label for="modalDb" class="form-label">Database</label>
            <select class="form-select" name="db" id="modalDb" required>
              <option value="MojoAdv">MojoAdv</option>
              <option value="AltroDb1">AltroDb1</option>
              <option value="AltroDb2">AltroDb2</option>
              <option value="AltroDb3">AltroDb3</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
          <button type="submit" class="btn btn-primary">Processa</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
function openDbModal(filename) {
    document.getElementById('modalFilename').value = filename;
    var dbModal = new bootstrap.Modal(document.getElementById('dbModal'));
    dbModal.show();
}
</script>
@endpush
@endsection 