@extends('layouts.main')

@section('title', $category->name)
@section('page-title', $category->name)
@section('page-description', $category->description ?? 'Detail kategori')

@section('content')

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h3 style="font-size: 24px; font-weight: bold;">{{ $category->name }}</h3>
            <p style="color: #6b7280; margin-top: 5px;">{{ $category->description }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-primary">Ubah</a>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

    <div style="background: #f9fafb; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
        <div class="grid-3">
            <div>
                <p style="font-size: 12px; color: #6b7280;">Total Dokumen</p>
                <p style="font-size: 24px; font-weight: bold; margin-top: 5px;">{{ $category->documents->count() }}</p>
            </div>
            <div>
                <p style="font-size: 12px; color: #6b7280;">Warna</p>
                <div style="margin-top: 8px; display: flex; align-items: center; gap: 10px;">
                    <div style="width: 30px; height: 30px; border-radius: 4px; background: {{ $category->color }};"></div>
                    <span style="font-weight: 600;">{{ $category->color }}</span>
                </div>
            </div>
            <div>
                <p style="font-size: 12px; color: #6b7280;">Dibuat</p>
                <p style="font-size: 14px; font-weight: 600; margin-top: 5px;">{{ $category->created_at->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    <h4 style="font-size: 18px; font-weight: bold; margin-bottom: 15px;">Dokumen di Kategori Ini</h4>

    @if($category->documents->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Tanggal Dokumen</th>
                    <th>Ukuran</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($category->documents as $doc)
                <tr>
                    <td>
                        <a href="{{ route('documents.show', $doc->id) }}" style="color: #3b82f6; text-decoration: none; font-weight: 600;">
                            {{ $doc->title }}
                        </a>
                    </td>
                    <td>{{ $doc->document_date ? $doc->document_date->format('d/m/Y') : '-' }}</td>
                    <td>{{ $doc->file_size_human }}</td>
                    <td>
                        @if($doc->status == 'active')
                            <span class="badge badge-green">Aktif</span>
                        @elseif($doc->status == 'expired')
                            <span class="badge badge-red">Kadaluarsa</span>
                        @else
                            <span class="badge badge-gray">Diarsipkan</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('documents.show', $doc->id) }}" style="color: #3b82f6; text-decoration: none; font-size: 13px;">Lihat →</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; background: #f9fafb; border-radius: 6px;">
            <p style="color: #6b7280;">Belum ada dokumen di kategori ini</p>
        </div>
    @endif
</div>

@endsection
