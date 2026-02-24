@extends('layouts.main')

@section('title', $document->title)
@section('page-title', $document->title)
@section('page-description', 'Detail dokumen arsip')

@section('content')

<div class="grid-2">
    <!-- Document Info -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
            <h3 style="font-size: 20px; font-weight: bold;">{{ $document->title }}</h3>
            @if($document->status == 'active')
                <span class="badge badge-green">Aktif</span>
            @elseif($document->status == 'expired')
                <span class="badge badge-red">Kadaluarsa</span>
            @else
                <span class="badge badge-gray">Diarsipkan</span>
            @endif
        </div>

        @if($document->description)
            <div style="background: #f9fafb; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                <p style="color: #374151; line-height: 1.6;">{{ $document->description }}</p>
            </div>
        @endif

        <table style="width: 100%;">
            <tr>
                <td style="padding: 10px 0; color: #6b7280; width: 40%;">Kategori</td>
                <td style="padding: 10px 0; font-weight: 600;">
                    <span class="badge" style="background: {{ $document->category->color }}20; color: {{ $document->category->color }};">
                        {{ $document->category->icon }} {{ $document->category->name }}
                    </span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0; color: #6b7280;">Nomor Dokumen</td>
                <td style="padding: 10px 0; font-weight: 600;">{{ $document->document_number ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 10px 0; color: #6b7280;">Tanggal Dokumen</td>
                <td style="padding: 10px 0; font-weight: 600;">{{ $document->document_date ? $document->document_date->format('d F Y') : '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 10px 0; color: #6b7280;">Tanggal Kadaluarsa</td>
                <td style="padding: 10px 0; font-weight: 600;">
                    @if($document->expiry_date)
                        {{ $document->expiry_date->format('d F Y') }}
                        @php
                            $daysUntil = (int) ceil(now()->diffInDays($document->expiry_date, false));
                        @endphp
                        @if($daysUntil < 0)
                            <span class="badge badge-red" style="margin-left: 10px;">Sudah Kadaluarsa</span>
                        @elseif($daysUntil <= 30)
                            <span class="badge badge-yellow" style="margin-left: 10px;">{{ $daysUntil }} hari lagi</span>
                        @endif
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0; color: #6b7280;">Nama File</td>
                <td style="padding: 10px 0; font-weight: 600;">{{ $document->file_name }}</td>
            </tr>
            <tr>
                <td style="padding: 10px 0; color: #6b7280;">Ukuran File</td>
                <td style="padding: 10px 0; font-weight: 600;">{{ $document->file_size_human }}</td>
            </tr>
            <tr>
                <td style="padding: 10px 0; color: #6b7280;">Tipe File</td>
                <td style="padding: 10px 0; font-weight: 600;">{{ strtoupper(pathinfo($document->file_name, PATHINFO_EXTENSION)) }}</td>
            </tr>
            <tr>
                <td style="padding: 10px 0; color: #6b7280;">Unduhan</td>
                <td style="padding: 10px 0; font-weight: 600;">{{ $document->download_count }}x</td>
            </tr>
            @if($document->tags && count($document->tags) > 0)
            <tr>
                <td style="padding: 10px 0; color: #6b7280;">Tags</td>
                <td style="padding: 10px 0;">
                    @foreach($document->tags as $tag)
                        <span class="badge badge-blue" style="margin-right: 5px;">{{ $tag }}</span>
                    @endforeach
                </td>
            </tr>
            @endif
            <tr>
                <td style="padding: 10px 0; color: #6b7280;">Dibuat pada</td>
                <td style="padding: 10px 0; font-weight: 600;">{{ $document->created_at->format('d F Y, H:i') }}</td>
            </tr>
            <tr>
                <td style="padding: 10px 0; color: #6b7280;">Diupdate pada</td>
                <td style="padding: 10px 0; font-weight: 600;">{{ $document->updated_at->format('d F Y, H:i') }}</td>
            </tr>
        </table>

        <div style="display: flex; gap: 10px; margin-top: 20px; flex-wrap: wrap;">
            <a href="{{ route('documents.download', $document->id) }}" class="btn btn-success">Unduh</a>
            <a href="{{ route('documents.edit', $document->id) }}" class="btn btn-primary">Ubah</a>
            <a href="{{ route('documents.index') }}" class="btn btn-secondary">Kembali</a>
        </div>

        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <form action="{{ route('documents.destroy', $document->id) }}" method="POST" onsubmit="return confirmDelete('Apakah Anda yakin ingin menghapus dokumen ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Hapus Dokumen</button>
            </form>
        </div>
    </div>

    <!-- Preview Section -->
    <div class="card">
        <h3 style="font-size: 18px; font-weight: bold; margin-bottom: 15px;">Pratinjau</h3>

        @if($document->file_type == 'application/pdf')
            <iframe src="{{ $document->file_url }}" style="width: 100%; height: 600px; border: 1px solid #e5e7eb; border-radius: 6px;"></iframe>
        @elseif(in_array($document->file_type, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif']))
            <img src="{{ $document->file_url }}" style="width: 100%; height: auto; border-radius: 6px; border: 1px solid #e5e7eb;">
        @else
            <div style="text-align: center; padding: 60px 20px; background: #f9fafb; border-radius: 6px;">
                <p style="color: #6b7280; margin-bottom: 8px;">Pratinjau tidak tersedia untuk tipe file ini</p>
                <p style="color: #9ca3af; font-size: 14px;">Silakan klik tombol <strong>Unduh</strong> untuk melihat isinya</p>
            </div>
        @endif
    </div>
</div>

@endsection
