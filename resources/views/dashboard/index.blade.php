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
                        <p class="mb-0">Dashboard Overview</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 row-cols-xxl-4">
    <div class="col">
        <div class="card radius-10">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="">
                        <p class="mb-1">Total Leads</p>
                        <h4 class="mb-0">{{ number_format($totalLeads) }}</h4>
                    </div>
                    <div class="ms-auto widget-icon bg-light-primary text-primary">
                        <i class="material-icons-outlined">person</i>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 75%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="">
                        <p class="mb-1">Total Movements</p>
                        <h4 class="mb-0">{{ number_format($totalMovements) }}</h4>
                    </div>
                    <div class="ms-auto widget-icon bg-light-success text-success">
                        <i class="material-icons-outlined">trending_up</i>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 85%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="mb-0">Recent Movements</h5>
                <div class="ms-auto">
                    <a href="{{ route('dashboard.downloadAll') }}" class="btn btn-primary">
                        <i class="material-icons-outlined">download</i> Download All
                    </a>
                    <a href="{{ route('dashboard.listDownloads') }}" class="btn btn-light">
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
                            @foreach($recentMovements as $move)
                                <tr>
                                    <td>{{ $move->id }}</td>
                                    <td>{{ $move->lead->email }}</td>
                                    <td>
                                        <span class="badge bg-light-primary text-primary">{{ $move->action }}</span>
                                    </td>
                                    <td>{{ $move->timeActionOpen }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection