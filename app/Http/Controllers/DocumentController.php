<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * KUK 017: Terstruktur - CRUD operations
 * KUK 018: OOP - Resource controller
 * KUK 030: Multimedia - File upload handling
 */
class DocumentController extends Controller
{
    /**
     * Display a listing of documents
     * KUK 020: SQL - Query dengan filtering dan search
     * KUK 022: Algoritma - Search dan filtering algorithm
     */
    public function index(Request $request)
    {
        // Filter by current user - setiap user hanya melihat dokumen miliknya
        $query = Document::with('category')->where('user_id', auth()->id());

        // Search (KUK 022: Search algorithm)
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->category($request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->status($request->status);
        }

        // Sort (KUK 022: Sorting algorithm)
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $documents = $query->paginate(20)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('documents.index', compact('documents', 'categories'));
    }

    /**
     * Show the form for creating a new document
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('documents.create', compact('categories'));
    }

    /**
     * Generate auto document number based on category
     * Format: KODE_KATEGORI/YYYY/MM/XXXX
     * Contoh: SM/2026/02/0001 (Surat Masuk)
     */
    private function generateDocumentNumber($categoryId)
    {
        $category = Category::find($categoryId);

        // Generate kode kategori dari nama (2-3 huruf pertama)
        $categoryCode = $this->generateCategoryCode($category->name);

        $year = date('Y');
        $month = date('m');
        $prefix = "{$categoryCode}/{$year}/{$month}/";

        // Get last document number for current month & category
        $lastDoc = Document::where('document_number', 'like', $prefix . '%')
            ->orderBy('document_number', 'desc')
            ->first();

        if ($lastDoc) {
            // Extract number and increment
            $lastNumber = intval(substr($lastDoc->document_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }

    /**
     * Generate category code from name
     */
    private function generateCategoryCode($name)
    {
        // Mapping kategori ke kode
        $mapping = [
            'surat masuk' => 'SM',
            'surat keluar' => 'SK',
            'invoice' => 'INV',
            'kontrak' => 'KTR',
            'laporan' => 'LPR',
            'sertifikat' => 'SRT',
        ];

        $nameLower = strtolower($name);

        // Cek apakah ada di mapping
        if (isset($mapping[$nameLower])) {
            return $mapping[$nameLower];
        }

        // Jika tidak, ambil 3 huruf pertama dari kata pertama
        $words = explode(' ', $name);
        $code = strtoupper(substr($words[0], 0, 3));

        return $code;
    }

    /**
     * Store a newly created document
     * KUK 030: Multimedia - File upload implementation
     * KUK 025: Debugging - Validation dan error handling
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'document_date' => 'nullable|date',
                'expiry_date' => 'nullable|date|after:document_date',
                'tags' => 'nullable|string',
                'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240', // Max 10MB
            ]);

            // Auto-generate document number based on category
            $validated['document_number'] = $this->generateDocumentNumber($validated['category_id']);

            // Handle file upload (KUK 030: Multimedia handling)
            if ($request->hasFile('file')) {
                $file = $request->file('file');

                // Generate unique filename
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

                // Store file
                $path = $file->storeAs('documents', $filename, 'public');

                $validated['file_name'] = $file->getClientOriginalName();
                $validated['file_path'] = $path;
                $validated['file_type'] = $file->getMimeType();
                $validated['file_size'] = $file->getSize();
            }

            // Process tags (KUK 022: String processing algorithm)
            if ($request->filled('tags')) {
                $validated['tags'] = array_map('trim', explode(',', $request->tags));
            }

            // Set user_id (document milik user yang sedang login)
            $validated['user_id'] = auth()->id();

            $document = Document::create($validated);

            return redirect()
                ->route('documents.index')
                ->with('success', 'Dokumen berhasil ditambahkan!');

        } catch (\Exception $e) {
            // KUK 025: Error handling dan logging
            \Log::error('Error creating document: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified document
     */
    public function show(Document $document)
    {
        // Authorization: hanya bisa melihat dokumen milik sendiri
        if ($document->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        $document->load('category');
        return view('documents.show', compact('document'));
    }

    /**
     * Show the form for editing the specified document
     */
    public function edit(Document $document)
    {
        // Authorization: hanya bisa edit dokumen milik sendiri
        if ($document->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        $categories = Category::orderBy('name')->get();
        return view('documents.edit', compact('document', 'categories'));
    }

    /**
     * Update the specified document
     * KUK 047: Update system implementation
     */
    public function update(Request $request, Document $document)
    {
        // Authorization: hanya bisa update dokumen milik sendiri
        if ($document->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        try {
            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'document_date' => 'nullable|date',
                'expiry_date' => 'nullable|date|after:document_date',
                'status' => 'required|in:active,archived,expired',
                'tags' => 'nullable|string',
                'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
            ]);

            // Handle file replacement (KUK 030: File management)
            if ($request->hasFile('file')) {
                // Delete old file
                Storage::disk('public')->delete($document->file_path);

                $file = $request->file('file');
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('documents', $filename, 'public');

                $validated['file_name'] = $file->getClientOriginalName();
                $validated['file_path'] = $path;
                $validated['file_type'] = $file->getMimeType();
                $validated['file_size'] = $file->getSize();
            }

            // Process tags
            if ($request->filled('tags')) {
                $validated['tags'] = array_map('trim', explode(',', $request->tags));
            }

            $document->update($validated);

            return redirect()
                ->route('documents.show', $document)
                ->with('success', 'Dokumen berhasil diupdate!');

        } catch (\Exception $e) {
            \Log::error('Error updating document: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified document
     * KUK 047: Soft delete
     */
    public function destroy(Document $document)
    {
        // Authorization: hanya bisa hapus dokumen milik sendiri
        if ($document->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        try {
            // Soft delete - file tetap ada di storage
            $document->delete();

            return redirect()
                ->route('documents.index')
                ->with('success', 'Dokumen berhasil dihapus!');

        } catch (\Exception $e) {
            \Log::error('Error deleting document: ' . $e->getMessage());

            return redirect()
                ->route('documents.index')
                ->with('error', 'Gagal menghapus dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Download document
     * KUK 022: Algoritma - Download counter increment
     */
    public function download(Document $document)
    {
        // Authorization: hanya bisa download dokumen milik sendiri
        if ($document->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        try {
            $document->incrementDownloadCount();

            return Storage::disk('public')->download(
                $document->file_path,
                $document->file_name
            );

        } catch (\Exception $e) {
            \Log::error('Error downloading document: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Gagal mendownload dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Preview document (for PDFs and images)
     * KUK 030: Multimedia display
     */
    public function preview(Document $document)
    {
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];

        if (!in_array($document->file_type, $allowedTypes)) {
            return redirect()
                ->back()
                ->with('error', 'Preview tidak tersedia untuk tipe file ini.');
        }

        return view('documents.preview', compact('document'));
    }
}
