@extends('layouts.main')

@section('title', 'Edit Dokumen')
@section('page-title', 'Edit Dokumen')
@section('page-description', 'Update informasi dokumen')

@section('content')

<div class="card" style="max-width: 800px;">
    <form action="{{ route('documents.update', $document->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">Kategori *</label>
            <select name="category_id" class="form-control" required>
                <option value="">Pilih Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $document->category_id) == $cat->id ? 'selected' : '' }}>
                        {{ $cat->icon }} {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Judul Dokumen *</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $document->title) }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">Tanggal Dokumen</label>
            <input type="date" name="document_date" class="form-control" value="{{ old('document_date', $document->document_date ? $document->document_date->format('Y-m-d') : '') }}">
        </div>

        <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $document->description) }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Tanggal Expired (Opsional)</label>
            <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date', $document->expiry_date ? $document->expiry_date->format('Y-m-d') : '') }}">
        </div>

        <div class="form-group">
            <label class="form-label">Status *</label>
            <select name="status" class="form-control" required>
                <option value="active" {{ old('status', $document->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="archived" {{ old('status', $document->status) == 'archived' ? 'selected' : '' }}>Diarsipkan</option>
                <option value="expired" {{ old('status', $document->status) == 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Tags (Opsional)</label>
            <input type="text" name="tags" class="form-control" value="{{ old('tags', is_array($document->tags) ? implode(', ', $document->tags) : '') }}" placeholder="penting, urgent, rahasia">
        </div>

        <div class="form-group">
            <label class="form-label">File Saat Ini</label>
            <div style="background: #f9fafb; padding: 15px; border-radius: 6px; margin-bottom: 10px;">
                <p style="font-weight: 600;">{{ $document->file_name }}</p>
                <p style="font-size: 13px; color: #6b7280; margin-top: 5px;">Ukuran: {{ $document->file_size_human }}</p>
            </div>

            <label class="form-label">Upload File Baru (Opsional - Max 10MB)</label>
            <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
            <small style="color: #6b7280; font-size: 12px;">Kosongkan jika tidak ingin mengganti file</small>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('documents.show', $document->id) }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

@endsection
