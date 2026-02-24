@extends('layouts.main')

@section('title', 'Dokumen')
@section('page-title', 'Manajemen Dokumen')
@section('page-description', 'Kelola arsip dokumen digital')

@section('content')

<div class="card">
    <!-- Search & Filter Form (KUK 022: Search/Filter algorithm) -->
    <form method="GET" action="{{ route('documents.index') }}" style="margin-bottom: 20px;">
        <div class="grid-4" style="margin-bottom: 15px;">
            <div class="form-group" style="margin: 0;">
                <input type="text" name="search" class="form-control" placeholder="Cari dokumen..." value="{{ request('search') }}">
            </div>

            <div class="form-group" style="margin: 0;">
                <select name="category" class="form-control">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin: 0;">
                <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Diarsipkan</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
                </select>
            </div>

            <div style="display: flex; gap: 5px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Cari</button>
                @if(request()->hasAny(['search', 'category', 'status']))
                    <a href="{{ route('documents.index') }}" class="btn btn-secondary">Reset</a>
                @endif
            </div>
        </div>
    </form>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="font-size: 18px; font-weight: 600; color: #1f2937;">Daftar Dokumen ({{ $documents->total() }})</h3>
        <a href="{{ route('documents.create') }}" class="btn btn-primary">+ Tambah Dokumen</a>
    </div>

    @if($documents->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>
                        <a href="{{ route('documents.index', array_merge(request()->all(), ['sort' => 'title', 'direction' => request('sort') == 'title' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                           style="color: inherit; text-decoration: none;">
                            Judul {{ request('sort') == 'title' ? (request('direction') == 'asc' ? '↑' : '↓') : '' }}
                        </a>
                    </th>
                    <th>Kategori</th>
                    <th>Nomor</th>
                    <th>
                        <a href="{{ route('documents.index', array_merge(request()->all(), ['sort' => 'document_date', 'direction' => request('sort') == 'document_date' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                           style="color: inherit; text-decoration: none;">
                            Tanggal {{ request('sort') == 'document_date' ? (request('direction') == 'asc' ? '↑' : '↓') : '' }}
                        </a>
                    </th>
                    <th>Ukuran</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($documents as $doc)
                <tr>
                    <td>
                        <a href="{{ route('documents.show', $doc->id) }}" style="color: #3b82f6; text-decoration: none; font-weight: 600;">
                            {{ Str::limit($doc->title, 50) }}
                        </a>
                        @if($doc->download_count > 0)
                            <br><small style="color: #6b7280;">{{ $doc->download_count }}x downloads</small>
                        @endif
                    </td>
                    <td>
                        <span class="badge" style="background: {{ $doc->category->color }}20; color: {{ $doc->category->color }};">
                            {{ $doc->category->name }}
                        </span>
                    </td>
                    <td style="font-size: 13px; color: #6b7280;">{{ $doc->document_number ?? '-' }}</td>
                    <td style="font-size: 13px;">{{ $doc->document_date ? $doc->document_date->format('d/m/Y') : '-' }}</td>
                    <td style="font-size: 13px;">{{ $doc->file_size_human }}</td>
                    <td>
                        @if($doc->status == 'active')
                            <span class="badge badge-success">Aktif</span>
                        @elseif($doc->status == 'expired')
                            <span class="badge badge-danger">Kadaluarsa</span>
                        @else
                            <span class="badge badge-warning">Diarsipkan</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 5px;">
                            <a href="{{ route('documents.show', $doc->id) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">Lihat</a>
                            <a href="{{ route('documents.download', $doc->id) }}" class="btn btn-success" style="padding: 6px 12px; font-size: 12px;">Unduh</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
            <p style="color: #6b7280; font-size: 14px;">
                Showing {{ $documents->firstItem() }} - {{ $documents->lastItem() }} of {{ $documents->total() }} documents
            </p>
            <div>
                {{ $documents->links() }}
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 60px 20px;">
            <p style="font-size: 18px; color: #6b7280; margin-bottom: 20px;">
                @if(request()->hasAny(['search', 'category', 'status']))
                    Tidak ada dokumen yang sesuai dengan filter
                @else
                    Belum ada dokumen
                @endif
            </p>
            @if(request()->hasAny(['search', 'category', 'status']))
                <a href="{{ route('documents.index') }}" class="btn btn-secondary">Reset Filter</a>
            @else
                <a href="{{ route('documents.create') }}" class="btn btn-primary">Tambah Dokumen Pertama</a>
            @endif
        </div>
    @endif
</div>

@endsection
