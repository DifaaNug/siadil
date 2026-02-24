<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User yang upload
            $table->string('title');
            $table->string('document_number')->unique()->nullable(); // Nomor surat/dokumen
            $table->text('description')->nullable();
            $table->string('file_name'); // Original filename
            $table->string('file_path'); // Storage path
            $table->string('file_type'); // mime type
            $table->unsignedBigInteger('file_size'); // in bytes
            $table->date('document_date')->nullable(); // Tanggal surat
            $table->date('expiry_date')->nullable(); // KUK 044: untuk alert notifikasi
            $table->enum('status', ['active', 'archived', 'expired'])->default('active');
            $table->text('tags')->nullable(); // JSON array for search
            $table->integer('download_count')->default(0); // KUK 022: Algoritma tracking
            $table->timestamps();
            $table->softDeletes(); // KUK 047: Soft delete

            // KUK 020: SQL Indexes untuk optimasi query
            $table->index(['category_id', 'status']);
            $table->index('user_id'); // Index untuk filter per user
            $table->index('document_date');
            $table->index('created_at');
            $table->index('title'); // Index for search
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
