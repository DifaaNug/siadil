@extends('layouts.main')

@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori Baru')
@section('page-description', 'Buat kategori baru untuk mengorganisir dokumen')

@section('content')

<div class="card" style="max-width: 600px;">
    <form action="{{ route('categories.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label class="form-label">Nama Kategori *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Contoh: Laporan Keuangan">
        </div>

        <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi kategori (opsional)">{{ old('description') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Warna *</label>
            <input type="color" name="color" class="form-control" value="{{ old('color', '#3490dc') }}" required style="height: 50px;">
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Simpan Kategori</button>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

@endsection
