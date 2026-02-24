@extends('layouts.main')

@section('title', 'Kategori')
@section('page-title', 'Manajemen Kategori')
@section('page-description', 'Kelola kategori dokumen')

@section('content')

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="font-size: 18px; font-weight: 600; color: #1f2937;">Daftar Kategori</h3>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">+ Tambah Kategori</a>
    </div>

    @if($categories->count() > 0)
        <div class="grid-3">
            @foreach($categories as $category)
            <div class="card" style="border-left: 4px solid {{ $category->color }}; background-color: {{ $category->color }}05;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <h4 style="font-size: 17px; font-weight: 600; color: #1f2937;">{{ $category->name }}</h4>
                    <span class="badge badge-gray">{{ $category->documents_count }} dok</span>
                </div>

                <p style="font-size: 13px; color: #6b7280; margin-bottom: 15px; min-height: 40px;">{{ $category->description ?? 'No description' }}</p>

                <div style="display: flex; gap: 8px;">
                    <a href="{{ route('categories.show', $category->id) }}" class="btn btn-secondary" style="flex: 1; text-align: center; padding: 8px 12px; font-size: 13px;">
                        Lihat
                    </a>
                    <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-primary" style="flex: 1; text-align: center; padding: 8px 12px; font-size: 13px;">
                        Ubah
                    </a>
                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="flex: 1;" onsubmit="return confirmDelete('Apakah Anda yakin ingin menghapus kategori ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="width: 100%; padding: 8px 12px; font-size: 13px;">Hapus</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 60px 20px;">
            <p style="font-size: 18px; color: #6b7280; margin-bottom: 20px;">Belum ada kategori</p>
            <a href="{{ route('categories.create') }}" class="btn btn-primary">Tambah kategori pertama</a>
        </div>
    @endif
</div>

@endsection
