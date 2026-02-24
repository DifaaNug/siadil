@extends('layouts.main')

@section('title', 'Ubah Kategori')
@section('page-title', 'Ubah Kategori')
@section('page-description', 'Perbarui informasi kategori')

@section('content')

<div class="card" style="max-width: 600px;">
    <form action="{{ route('categories.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">Nama Kategori *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $category->description) }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Warna *</label>
            <input type="color" name="color" class="form-control" value="{{ old('color', $category->color) }}" required style="height: 50px;">
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Perbarui Kategori</button>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

@endsection
