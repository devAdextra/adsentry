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
                        <h6 class="mb-0">Welcome back</h6>
                        <p class="mb-0">Scoring Overview</p>
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
                <h5 class="mb-0">Carica File</h5>
                <div class="ms-auto">
                    <a href="" class="btn btn-primary">
                        <i class="material-icons-outlined">download</i> Download All
                    </a>
                    <a href="" class="btn btn-light">
                        <i class="material-icons-outlined">folder</i> View Downloads
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Lead Email</th>
                                <th>Action</th>
                                <th>Time Open</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(session('success'))
                            <p style="color:green">{{ session('success') }}</p>
                        @endif

                        <form action="{{ route('upload.handle') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <label for="upload_file">Seleziona il file:</label>
                            <input type="file" name="upload_file" id="upload_file" required>
                            <button type="submit">Carica</button>
                        </form>
                    </body>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection