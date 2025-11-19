<?php

namespace App\Http\Controllers;

use App\Models\Books;
use App\Models\Bookitems;
use App\Models\Racks;
use Illuminate\Http\Request;

class BukuItemsController extends Controller
{
    public function index($book)
    {
        $book = Books::with('bookitems')->findOrFail($book);

        // Ambil items per buku dengan paginate DAN relasi racks + lokasiRak
        $items = $book->bookitems()
            ->with(['racks.rackslocation']) // ✅ Load relasi lengkap
            ->orderBy('id_item', 'asc')
            ->paginate(10)
            ->withQueryString();

        // ✅ Kirim data racks ke view dengan relasi location
        $racks = Racks::with('rackslocation')->get();

        return view('bookitems.index', compact('book', 'items', 'racks'));
    }

    public function create($book)
    {
        $book = Books::findOrFail($book);
        $racks = Racks::with('rackslocation')->get(); // ✅ Ganti variable name & load relasi

        return view('books.items.create', compact('book', 'racks'));
    }

    public function store(Request $request, $id_buku)
    {
        $data = $request->validate([
            'kondisi' => 'required|in:baik,rusak,hilang',
            'status' => 'nullable|in:tersedia,dipinjam,hilang',
            'sumber' => 'nullable|string|max:255',
            'id_rak' => 'nullable|exists:racks,id_rak', // ✅ Ganti jadi nullable karena bisa belum ditata
        ]);

        Bookitems::create([
            'id_buku'   => $id_buku,
            'id_rak'    => $request->id_rak,
            'kondisi'   => $request->kondisi,
            'status'    => $request->status ?? 'tersedia', // ✅ Default tersedia
            'sumber'    => $request->sumber
        ]);

        return redirect()->route('books.items.index', $id_buku)
            ->with('success', 'Item buku berhasil ditambahkan.');
    }

    public function show($book, $item)
    {
        $book = Books::findOrFail($book);
        $item = Bookitems::where('id_buku', $book->id_buku)
            ->where('id_item', $item)
            ->with(['racks.rackslocation']) // ✅ Load relasi
            ->firstOrFail();

        return view('books.items.show', compact('book', 'item'));
    }

    public function edit($book, $item)
    {
        $book = Books::findOrFail($book);
        $item = Bookitems::where('id_buku', $book->id_buku)
            ->with(['racks.rackslocation']) // ✅ Load relasi
            ->findOrFail($item);

        $racks = Racks::with('rackslocation')->get(); // ✅ Kirim data racks

        return view('books.items.edit', compact('book', 'item', 'racks'));
    }

    public function update(Request $request, $book, $id_item)
    {
        $data = $request->validate([
            'kondisi' => 'required|in:baik,rusak,hilang',
            'status' => 'required|in:tersedia,dipinjam,hilang',
            'sumber' => 'nullable|string|max:255',
            'id_rak' => 'nullable|exists:racks,id_rak', // ✅ Nullable
        ]);

        $item = Bookitems::findOrFail($id_item);
        $item->update($data);

        return redirect()->route('books.items.index', $item->id_buku)
            ->with('success', 'Item buku berhasil diperbarui.');
    }

    public function destroy($book, $item)
    {
        $item = Bookitems::where('id_buku', $book)->findOrFail($item);
        $item->delete();

        return redirect()->route('books.items.index', $book)
            ->with('success', 'Item berhasil dihapus');
    }

    public function allItems(Request $request)
    {
        $query = Bookitems::with(['books', 'racks.rackslocation']); // ✅ Load relasi lengkap

        // Kalau ada id_buku di query string, filter berdasarkan itu
        if ($request->has('id_buku')) {
            $query->where('id_buku', $request->id_buku);
        }

        $items = $query->get();

        return view('books.all', compact('items'));
    }

    public function pinjam(Request $request, $id)
    {
        $user = auth()->user();

        if (!in_array($user->role, ['member', 'petugas', 'admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya member/petugas/admin yang boleh meminjam.'
            ], 403);
        }

        $validated = $request->validate([
            'pengembalian' => 'required|date|after:today'
        ]);

        $item = Bookitems::findOrFail($id);

        if ($item->status !== 'tersedia') {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak tersedia untuk dipinjam.'
            ]);
        }

        // Buat record borrowing baru (status pending)
        \App\Models\Borrowing::create([
            'id_user' => $user->id_user,
            'id_item' => $item->id_item,
            'id_buku' => $item->id_buku,
            'pinjam' => now(),
            'pengembalian' => $validated['pengembalian'],
            'status' => 'pending',
            'alamat' => $request->alamat,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan peminjaman berhasil dikirim, menunggu persetujuan.'
        ]);
    }
}
