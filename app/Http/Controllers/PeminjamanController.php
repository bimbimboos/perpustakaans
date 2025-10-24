<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Books;
use App\Models\Bookitems;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    public function index()
    {
        $borrowing = Borrowing::with(['user', 'buku', 'item'])->get();
        $books = Books::all();
        $users = User::all();
        $bookitems = Bookitems::all();
        return view('borrowing.index', compact('borrowing', 'books', 'users', 'bookitems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_buku' => 'required|exists:books,id_buku',
            'id_item' => 'required|exists:bookitems,id_item',
            'id_user' => 'required|exists:users,id_user',
            'pengembalian' => 'required|date|after:today',
        ]);

        $bookitems = Bookitems::findOrFail($request->id_item);

        if ($bookitems->status !== 'tersedia') {
            return back()->with('error', 'Eksemplar ini sedang dipinjam.');
        }

        $borrowing = Borrowing::create([
            'id_user' => $request->id_user,
            'id_buku' => $request->id_buku,
            'id_item' => $request->id_item,
            'pinjam' => now(),
            'pengembalian' => $request->pengembalian,
            'status' => 'dipinjam',
        ]);

        $bookitems->update(['status' => 'dipinjam']);

        return back()->with('success', 'Peminjaman berhasil dibuat.');
    }

    public function destroy($id)
    {
        $borrowing = Borrowing::findOrFail($id);

        if ($borrowing->bookitems) {
            $borrowing->bookitems->update(['status' => 'tersedia']);
        }

        $borrowing->delete();

        return back()->with('success', 'Data peminjaman dihapus & status eksemplar dikembalikan.');
    }
}
