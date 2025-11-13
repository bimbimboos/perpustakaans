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
        $book = books::with('Bookitems')->findOrFail($book);

        // Ambil items per buku dengan paginate DAN relasi racks + lokasiRak
        $items = $book->bookitems()
            ->with(['racks.rackslocation']) // ✅ Tambah relasi ini
            ->orderBy('id_item', 'asc')
            ->paginate(10)
            ->withQueryString();

        $racks = racks::all();

        return view('bookitems.index', compact('book', 'items', 'racks'));
    }

    public function create($book)
    {
        $book = books::findOrFail($book);
        $rack = racks::all();
        return view('books.items.create', compact('book','rack'));
    }

    public function store(Request $request, $id_buku)
    {
        $data = $request->validate([
            'kondisi' => 'required|in:baik,rusak,hilang',
            'status' => 'required|in:tersedia,dipinjam,hilang',
            'sumber' => 'nullable|string|max:255',
            'id_rak' => 'required|exists:racks,id_rak',
        ]);
        Bookitems::create([
            'id_buku'   => $id_buku,
            'id_rak'    => $request->id_rak,
            'kondisi'   => $request->kondisi,
            'status'    => $request->status,
            'sumber'    => $request->sumber]);

        return redirect()->route('books.items.index', $id_buku)
            ->with('success', 'Item buku berhasil ditambahkan.');
    }



    public function show($book, $item)
    {
        $book = books::findOrFail($book);
        $item = Bookitems::where('id_buku', $book->id_buku)
            ->where('id_item', $item)
            ->firstOrFail();

        return view('books.items.show', compact('book','item'));
    }

    public function edit($book, $item)
    {
        $book = books::findOrFail($book);
        $item = Bookitems::where('id_buku', $book->id_buku)->findOrFail($item);

        return view('books.items.edit', compact('book','item'));
    }

    public function update(Request $request, $book, $id_item)
    {
        $data = $request->validate([
            'kondisi' => 'required|in:baik,rusak,hilang',
            'status' => 'required|in:tersedia,dipinjam,hilang',
            'sumber' => 'nullable|string|max:255',
            'id_rak' => 'required|exists:racks,id_rak',
        ]);
        $item = \App\Models\Bookitems::findOrFail($id_item);
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
        $query = \App\Models\Bookitems::with(['books', 'racks']);

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
        if (!in_array($user->role, ['member','petugas','admin'])) {
            return response()->json(['success'=>false,'message'=>'Hanya member/petugas/admin yang boleh meminjam.'],403);
        }

        $validated = $request->validate([
            'pengembalian' => 'required|date|after:today'
        ]);

        $item = Bookitems::findOrFail($id);
        if ($item->status !== 'tersedia') {
            return response()->json(['success'=>false,'message'=>'Item tidak tersedia untuk dipinjam.']);
        }

        // buat record borrowing baru (status pending)
        \App\Models\Borrowing::create([
            'id_user' => $user->id_user,
            'id_item' => $item->id_item,
            'id_buku' => $item->id_buku,
            'pinjam' => now(),
            'pengembalian' => $validated['pengembalian'],
            'status' => 'pending',
            'alamat' => $request->alamat,
        ]);

        return response()->json(['success'=>true,'message'=>'Permintaan borrowing dikirim, menunggu persetujuan admin/petugas.']);
    }


}
