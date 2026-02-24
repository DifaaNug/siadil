@extends('layouts.main')

@section('title', 'Log Aktivitas')
@section('page-title', 'Log Aktivitas')
@section('page-description', 'Riwayat aktivitas sistem untuk tracking dan monitoring')

@section('content')

<div class="card">
    <!-- Filter Form -->
    <form method="GET" action="{{ route('logs.index') }}" style="margin-bottom: 20px;">
        <div class="grid-4">
            <div class="form-group" style="margin: 0;">
                <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
            </div>

            <div class="form-group" style="margin: 0;">
                <select name="action" class="form-control">
                    <option value="">Semua Aksi</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                            {{ strtoupper($action) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin: 0;">
                <input type="date" name="from_date" class="form-control" placeholder="Dari Tanggal" value="{{ request('from_date') }}">
            </div>

            <div style="display: flex; gap: 5px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Filter</button>
                @if(request()->hasAny(['search', 'action', 'from_date']))
                    <a href="{{ route('logs.index') }}" class="btn btn-secondary">Reset</a>
                @endif
            </div>
        </div>
    </form>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="font-size: 18px; font-weight: bold;">Log Aktivitas ({{ $logs->total() }})</h3>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('logs.export') }}" class="btn btn-success">Ekspor CSV</a>
            <form action="{{ route('logs.clear') }}" method="POST" style="display: inline;" onsubmit="return confirmDelete('Yakin ingin menghapus log lama (>30 hari)?')">
                @csrf
                <button type="submit" class="btn btn-danger">Hapus Log Lama</button>
            </form>
        </div>
    </div>

    @if($logs->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 150px;">Waktu</th>
                    <th style="width: 100px;">Aksi</th>
                    <th>Deskripsi</th>
                    <th style="width: 120px;">Alamat IP</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td style="font-size: 13px; color: #6b7280;">
                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                        <br>
                        <small style="color: #9ca3af;">{{ $log->time_ago }}</small>
                    </td>
                    <td>
                        @if($log->action == 'create')
                            <span class="badge badge-green">CREATE</span>
                        @elseif($log->action == 'update')
                            <span class="badge badge-blue">UPDATE</span>
                        @elseif($log->action == 'delete')
                            <span class="badge badge-red">DELETE</span>
                        @elseif($log->action == 'download')
                            <span class="badge badge-purple">DOWNLOAD</span>
                        @else
                            <span class="badge badge-gray">{{ strtoupper($log->action) }}</span>
                        @endif
                    </td>
                    <td style="font-size: 14px;">
                        {{ $log->description }}
                        @if($log->model_type)
                            <br><small style="color: #9ca3af;">{{ class_basename($log->model_type) }} #{{ $log->model_id }}</small>
                        @endif
                    </td>
                    <td style="font-size: 13px; color: #6b7280;">{{ $log->ip_address ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div style="margin-top: 20px;">
            {{ $logs->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 60px 20px;">
            <p style="font-size: 18px; color: #6b7280;">Tidak ada log ditemukan</p>
        </div>
    @endif
</div>

@endsection
