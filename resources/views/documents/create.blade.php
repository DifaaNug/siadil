@extends('layouts.main')

@section('title', 'Tambah Dokumen')
@section('page-title', 'Tambah Dokumen Baru')
@section('page-description', 'Upload dokumen baru ke arsip digital')

@section('content')

<div class="card" style="max-width: 800px;">
    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label class="form-label">Kategori *</label>
            <select name="category_id" class="form-control" required>
                <option value="">Pilih Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Judul Dokumen *</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="Contoh: Surat Keputusan Nomor 001/2026">
        </div>

        <div class="form-group">
            <label class="form-label">Tanggal Dokumen</label>
            <input type="date" name="document_date" class="form-control" value="{{ old('document_date') }}">
        </div>

        <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi dokumen (opsional)">{{ old('description') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Tanggal Expired (Opsional)</label>
            <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date') }}">
            <small style="color: #6b7280; font-size: 12px;">Sistem akan otomatis update status jika dokumen sudah expired</small>
        </div>

        <div class="form-group">
            <label class="form-label">Tags (Opsional)</label>
            <input type="text" name="tags" class="form-control" value="{{ old('tags') }}" placeholder="penting, urgent, rahasia (pisahkan dengan koma)">
            <small style="color: #6b7280; font-size: 12px;">Pisahkan dengan koma untuk multiple tags</small>
        </div>

        <div class="form-group">
            <label class="form-label">File Dokumen * (Max 10MB)</label>
            <input type="file" name="file" class="form-control" required accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
            <small style="color: #6b7280; font-size: 12px;">
                Format: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG.<br>
                <strong>Nomor dokumen otomatis:</strong> Format KODE_KATEGORI/YYYY/MM/XXXX
                (Contoh: SM/2026/02/0001 untuk Surat Masuk)
            </small>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Simpan Dokumen</button>
            <a href="{{ route('documents.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

@endsection
