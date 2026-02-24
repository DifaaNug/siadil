@extends('layouts.main')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Ringkasan dan statistik arsip digital')

@section('content')

<!-- Statistics Cards -->
<div class="grid-4">
    <div class="card" style="border-left: 4px solid #3b82f6; background: linear-gradient(135deg, #ffffff 0%, #eff6ff 100%);">
        <div style="font-size: 13px; color: #6b7280; margin-bottom: 8px; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Total Dokumen</div>
        <div style="font-size: 36px; font-weight: bold; color: #3b82f6;">{{ $totalDocuments }}</div>
    </div>

    <div class="card" style="border-left: 4px solid #10b981; background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);">
        <div style="font-size: 13px; color: #6b7280; margin-bottom: 8px; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Dokumen Aktif</div>
        <div style="font-size: 36px; font-weight: bold; color: #10b981;">{{ $activeDocuments }}</div>
    </div>

    <div class="card" style="border-left: 4px solid #f59e0b; background: linear-gradient(135deg, #ffffff 0%, #fffbeb 100%);">
        <div style="font-size: 13px; color: #6b7280; margin-bottom: 8px; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Kategori</div>
        <div style="font-size: 36px; font-weight: bold; color: #f59e0b;">{{ $totalCategories }}</div>
    </div>

    <div class="card" style="border-left: 4px solid #8b5cf6; background: linear-gradient(135deg, #ffffff 0%, #faf5ff 100%);">
        <div style="font-size: 13px; color: #6b7280; margin-bottom: 8px; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Total Ukuran</div>
        <div style="font-size: 36px; font-weight: bold; color: #8b5cf6;">{{ number_format($totalSize / 1024 / 1024, 2) }} <span style="font-size: 16px; color: #6b7280;">MB</span></div>
    </div>
</div>

<!-- Recent Documents -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="font-size: 18px; font-weight: 600; color: #1f2937;">Dokumen Terbaru</h3>
        <a href="{{ route('documents.index') }}" class="btn btn-primary" style="padding: 8px 16px; font-size: 13px;">Lihat Semua</a>
    </div>

    @if($recentDocuments->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentDocuments as $doc)
                <tr>
                    <td>
                        <a href="{{ route('documents.show', $doc->id) }}" style="color: #3b82f6; text-decoration: none; font-weight: 600;">
                            {{ $doc->title }}
                        </a>
                    </td>
                    <td>
                        <span class="badge badge-gray" style="background-color: {{ $doc->category->color }}20; color: {{ $doc->category->color }};">
                            {{ $doc->category->name }}
                        </span>
                    </td>
                    <td>{{ $doc->created_at->format('d M Y') }}</td>
                    <td>
                        @if($doc->status == 'active')
                            <span class="badge badge-success">Aktif</span>
                        @elseif($doc->status == 'archived')
                            <span class="badge badge-warning">Diarsipkan</span>
                        @else
                            <span class="badge badge-danger">Kadaluarsa</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('documents.show', $doc->id) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">Lihat</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; color: #6b7280; padding: 40px 0;">Belum ada dokumen. Mulai dengan menambahkan dokumen baru!</p>
    @endif
</div>

<!-- Category Overview -->
<div class="card">
    <h3 style="font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 20px;">Dokumen per Kategori</h3>

    @if($categories->count() > 0)
        <div class="grid-3">
            @foreach($categories as $category)
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background-color: {{ $category->color }}10; border-radius: 8px; border-left: 3px solid {{ $category->color }};">
                <span style="font-weight: 600; color: #1f2937;">{{ $category->name }}</span>
                <span class="badge badge-gray">{{ $category->documents_count }} dok</span>
            </div>
            @endforeach
        </div>
    @else
        <p style="text-align: center; color: #6b7280; padding: 20px 0;">Belum ada kategori.</p>
    @endif
</div>

<!-- Recent Activities -->
<div class="card">
    <h3 style="font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 20px;">Recent Activities</h3>

    @if($recentActivities->count() > 0)
        <div style="max-height: 300px; overflow-y: auto;">
            @foreach($recentActivities as $activity)
            <div style="padding: 12px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: start;">
                <div style="flex: 1;">
                    <span class="badge badge-gray" style="font-size: 11px; text-transform: uppercase;">
                        @if($activity->action == 'create') Create
                        @elseif($activity->action == 'update') Update
                        @elseif($activity->action == 'delete') Delete
                        @else Action
                        @endif
                    </span>
                    <div style="font-size: 14px; color: #1f2937; margin-top: 5px;">{{ $activity->description }}</div>
                    <div style="font-size: 12px; color: #6b7280; margin-top: 3px;">{{ $activity->created_at->diffForHumans() }}</div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <p style="text-align: center; color: #6b7280; padding: 20px 0;">No activities yet.</p>
    @endif
</div>

@endsection
