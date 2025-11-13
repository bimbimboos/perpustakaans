<?php

namespace App\Http\Controllers;

use App\Models\sortbooks; // Import model sortbooks untuk operasi database
use App\Models\books; // Import model books untuk relasi dan dropdown
use App\Models\racks;// Import model racks untuk relasi dan dropdown
use App\Models\Bookitems; // Atau use App\Models\BukuItems; jika pakai camelCase
use App\Models\User;
use Illuminate\Http\Request; // Import Request untuk menangani input form
use Illuminate\Support\Facades\Validator; // Import Validator untuk validasi input
use Illuminate\Support\Facades\Auth; // Untuk auto user login
use Illuminate\Support\Facades\DB;

class PenataanBukusController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar sortbooks buku dengan pencarian dan pagination.
     */
    public function index(Request $request)
    {
        // Ambil query pencarian dari request
        $search = $request->query('search');

        // Buat query dengan relasi books, racks, dan user
        $query = sortbooks::with(['books', 'racks', 'user']);

        // Terapkan filter pencarian jika ada
        if ($search) {
            $query->whereHas('books', function ($q) use ($search) {
                $q->where('judul', 'like', '%' . $search . '%');  // Sesuaikan field buku
            })->orWhereHas('racks', function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%');
            })->orWhereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        // Ambil data dengan pagination (10 per halaman)
        $sortbooks = $query->paginate(10);

        // Ambil data buku dan rak untuk dropdown di modal
        $book = books::all();
        $rack = racks::all();

        // Kembalikan view index dengan data
        return view('sortbooks.index', compact('sortbooks', 'book', 'rack'));
    }

    /**
     * Show the form for creating a new resource.
     * Menampilkan form untuk membuat sortbooks buku baru.
     */
    public function create()
    {
        // Ambil data buku dan rak untuk dropdown
        $book = books::all();
        $rack = racks::all();

        // Kembalikan view create
        return view('sortbooks.create', compact('book', 'rack'));
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan data sortbooks buku baru ke database setelah validasi.
     */
    public function store(Request $request)
    {
        // Validasi input dasar (sudah ada, ok)
        $validator = Validator::make($request->all(), [
            'id_buku' => 'required|exists:books,id_buku',
            'id_rak' => 'required|exists:racks,id_rak',
            'kolom' => 'required|integer|min:1',
            'baris' => 'required|integer|min:1',
            'jumlah' => 'required|integer|min:1',
            'sumber' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $book = books::findOrFail($request->id_buku);
        $rack = racks::findOrFail($request->id_rak);

        // 1) Pastikan kategori sama (sudah ada, ok)
        if ($book->id_kategori != $rack->id_kategori) {
            return redirect()->back()
                ->withErrors(['id_rak' => 'Rak tidak sesuai kategori buku. Pilih rak yang kategorinya sama.'])
                ->withInput();
        }

        // BARU: Validasi stok buku total (sum semua sortbooks + new)
        $existingSumBuku = $book->sortbooks()->sum('jumlah');  // Sum existing sortbooks untuk buku ini
        $totalRequestedBuku = $existingSumBuku + $request->jumlah;
        if ($totalRequestedBuku > $book->jumlah) {
            return redirect()->back()
                ->withErrors(['jumlah' => "Jumlah melebihi total eksemplar buku! Sisa: " . ($book->jumlah - $existingSumBuku) . "."])
                ->withInput();
        }

        // BARU: Validasi kapasitas rak (sum semua sortbooks di rak + new)
        $existingSumRack = $rack->sortbooks()->sum('jumlah');
        $totalRequestedRack = $existingSumRack + $request->jumlah;
        if ($totalRequestedRack > $rack->kapasitas) {
            return redirect()->back()
                ->withErrors(['jumlah' => "Jumlah melebihi kapasitas rak! Sisa: " . ($rack->kapasitas - $existingSumRack) . "."])
                ->withInput();
        }

        // Lanjut transaction (kode lama ok, tapi tambah komentar)
        DB::transaction(function() use ($request, $book, $rack) {
            // Cek existing sortbooks di posisi persis sama (rak, kolom, baris, buku)
            $existingPenataan = sortbooks::where('id_buku', $request->id_buku)
                ->where('id_rak', $request->id_rak)
                ->where('kolom', $request->kolom)
                ->where('baris', $request->baris)
                ->first();

            if ($existingPenataan) {
                // Merge: Tambah jumlah ke existing
                $existingPenataan->jumlah += $request->jumlah;
                $existingPenataan->id_user = Auth::id();
                $existingPenataan->modified_date = now();
                $existingPenataan->save();

                $desiredTotal = $existingPenataan->jumlah;
            } else {
                // Buat baru
                $sortbooks = new sortbooks();
                $sortbooks->id_buku = $request->id_buku;
                $sortbooks->id_rak = $request->id_rak;
                $sortbooks->kolom = $request->kolom;
                $sortbooks->baris = $request->baris;
                $sortbooks->jumlah = $request->jumlah;
                $sortbooks->sumber = $request->sumber;
                $sortbooks->id_user = Auth::id();
                $sortbooks->insert_date = now();
                $sortbooks->modified_date = now();
                $sortbooks->save();

                $desiredTotal = $sortbooks->jumlah;
            }

            // Hitung existing items di rak ini untuk buku ini
            $existingItemsCount = Bookitems::where('id_buku', $request->id_buku)
                ->where('id_rak', $request->id_rak)
                ->count();

            // Delta: Buat item baru sebanyak kekurangan (sudah aman karena validasi atas)
            $toCreate = max(0, $desiredTotal - $existingItemsCount);

            for ($i = 0; $i < $toCreate; $i++) {
                Bookitems::create([
                    'id_buku' => $request->id_buku,
                    'id_rak' => $request->id_rak,
                    'kondisi' => 'baik',
                    'status' => 'tersedia',
                    'sumber' => $request->sumber,
                ]);
            }
        });

        return redirect()->route('sortbooks.index')->with('success', 'Penataan berhasil disimpan dan eksemplar dibuat.');
    }


    /**
     * Display the specified resource.
     * Menampilkan detail sortbooks buku berdasarkan ID.
     */
    public function show(string $id)
    {
        // Ambil data sortbooks dengan relasi
        $sortbooks = sortbooks::with(['books', 'racks', 'user'])->findOrFail($id);

        // Kembalikan view show
        return view('sortbooks.show', compact('sortbooks'));
    }

    /**
     * Show the form for editing the specified resource.
     * Menampilkan form untuk mengedit sortbooks buku.
     */
    public function edit(string $id)
    {
        // Ambil data sortbooks dan data untuk dropdown
        $sortbooks = sortbooks::findOrFail($id);
        $book = books::all();
        $rack = racks::all();

        // Kembalikan view edit
        return view('sortbooks.edit', compact('sortbooks', 'book', 'rack'));
    }

    /**
     * Update the specified resource in storage.
     * Memperbarui data sortbooks buku di database.
     */
    public function update(Request $request, string $id)
    {
        // Validasi input dasar (sama seperti store)
        $validator = Validator::make($request->all(), [
            'id_buku' => 'required|exists:books,id_buku',
            'id_rak' => 'required|exists:racks,id_rak',
            'kolom' => 'required|integer|min:1',
            'baris' => 'required|integer|min:1',
            'jumlah' => 'required|integer|min:1',
            'sumber' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $sortbooks = sortbooks::findOrFail($id);
        $oldJumlah = $sortbooks->jumlah;
        $oldRackId = $sortbooks->id_rak;

        // Di update(), setelah $sortbooks = sortbooks::findOrFail($id);
        $oldBukuId = $sortbooks->id_buku;
        $isBukuChanged = ($oldBukuId != $request->id_buku);

        if ($isBukuChanged) {
            // Pindah item lama ke buku baru (asumsi jumlah tetap, pindah sebanyak oldJumlah)
            Bookitems::where('id_buku', $oldBukuId)
                ->where('id_rak', $oldRackId)
                ->take($oldJumlah)
                ->update(['id_buku' => $request->id_buku]);
        }

        $book = books::withSum(['sortbooks' => fn($q) => $q->where('id_penataan', '!=', $id)], 'jumlah')->findOrFail($request->id_buku);
        $existingSumBuku = $book->sortbooks_sum_jumlah ?? 0;
        $totalRequestedBuku = $existingSumBuku + $request->jumlah;

        // Validasi jumlah buku total
        if ($totalRequestedBuku > $book->jumlah) {
            return redirect()->back()
                ->withErrors(['jumlah' => "Jumlah melebihi total eksemplar buku! Sisa: " . ($book->jumlah - $existingSumBuku) . "."])
                ->withInput();
        }

        // Validasi kapasitas rak (hitung exclude old jika rak berubah)
        $rack = racks::withSum(['sortbooks' => fn($q) => $q->where('id_penataan', '!=', $id)], 'jumlah')->findOrFail($request->id_rak);
        $existingSumRack = $rack->sortbooks_sum_jumlah ?? 0;
        $totalRequestedRack = $existingSumRack + $request->jumlah;
        if ($totalRequestedRack > $rack->kapasitas) {
            return redirect()->back()
                ->withErrors(['jumlah' => "Jumlah melebihi kapasitas rak! Sisa: " . ($rack->kapasitas - $existingSumRack) . "."])
                ->withInput();
        }

        // Cek jika lokasi berubah
        $isLocationChanged = ($sortbooks->id_rak != $request->id_rak || $sortbooks->kolom != $request->kolom || $sortbooks->baris != $request->baris);

        if ($isLocationChanged) {
            // Cek target lokasi untuk merge
            $targetPenataan = sortbooks::where('id_buku', $request->id_buku)
                ->where('id_rak', $request->id_rak)
                ->where('kolom', $request->kolom)
                ->where('baris', $request->baris)
                ->where('id_penataan', '!=', $id)
                ->first();

            if ($targetPenataan) {
                // Merge: Update target, hapus old sortbooks, pindah items
                $newJumlahTarget = $targetPenataan->jumlah + $request->jumlah;
                $targetPenataan->jumlah = $newJumlahTarget;
                $targetPenataan->id_user = Auth::id();
                $targetPenataan->modified_date = now();
                $targetPenataan->save();

                // Pindah items existing ke rak baru
                Bookitems::where('id_buku', $sortbooks->id_buku)
                    ->where('id_rak', $oldRackId)
                    ->take($oldJumlah) // Asumsi items sesuai jumlah old
                    ->update(['id_rak' => $request->id_rak]);

                $sortbooks->delete();

                return redirect()->route('sortbooks.index')->with('success', 'Penataan merged & dipindah. Eksemplar updated.');
            }
        }

        // Jika jumlah berubah (tidak merge), adjust eksemplar
        $delta = $request->jumlah - $oldJumlah;
        if ($delta > 0) {
            // Tambah items baru
            for ($i = 0; $i < $delta; $i++) {
                Bookitems::create([
                    'id_buku' => $request->id_buku,
                    'id_rak' => $request->id_rak,
                    'kondisi' => 'baik',
                    'status' => 'tersedia',
                    'sumber' => $request->sumber,
                ]);
            }
        } elseif ($delta < 0) {
            // Hapus items excess (asumsi hapus yang terakhir/tersedia)
            Bookitems::where('id_buku', $request->id_buku)
                ->where('id_rak', $request->id_rak)
                ->where('status', 'tersedia') // Hanya hapus yang tersedia
                ->orderBy('id_item', 'desc')
                ->take(abs($delta))
                ->delete();
        }

        // Update sortbooks
        $sortbooks->id_buku = $request->id_buku;
        $sortbooks->id_rak = $request->id_rak;
        $sortbooks->kolom = $request->kolom;
        $sortbooks->baris = $request->baris;
        $sortbooks->jumlah = $request->jumlah;
        $sortbooks->sumber = $request->sumber;
        $sortbooks->id_user = Auth::id();
        $sortbooks->modified_date = now();
        $sortbooks->save();

        // 🟢 Sinkronkan sumber di books
        Bookitems::where('id_buku', $request->id_buku)
            ->where('id_rak', $request->id_rak)
            ->update(['sumber' => $request->sumber]);

        return redirect()->route('sortbooks.index')->with('success', 'Penataan diperbarui. Eksemplar adjusted.');
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus data sortbooks dari database.
     */
    public function destroy(string $id)
    {
        $sortbooks = Sortbooks::findOrFail($id);

        // Hapus eksemplar terkait (asumsi hapus sebanyak jumlah)
        Bookitems::where('id_buku', $sortbooks->id_buku)
            ->where('id_rak', $sortbooks->id_rak)
            ->take($sortbooks->jumlah)
            ->delete();

        $sortbooks->delete();

        return redirect()->route('sortbooks.index')->with('success', 'Penataan dihapus. Eksemplar terkait dihapus.');
    }

    public function getRakByBuku($id_buku)
    {
        $book = \App\Models\books::find($id_buku);
        if (!$book) {
            return response()->json([]);
        }

        // Misal: ambil rak dengan kategori yang sama dengan buku
        $rack = \App\Models\racks::where('id_kategori', $book->id_kategori)->get();

        return response()->json($rack);
    }



    // ... (show dan edit tetap sama)

}
