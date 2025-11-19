<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Books;
use App\Models\Bookitems;
use App\Models\Members;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    /**
     * INDEX - semua role bisa akses, tapi member hanya lihat data miliknya
     */
    public function index()
    {
        // ambil semua peminjaman dengan relasi
        if (Auth::check() && in_array(Auth::user()->role, ['admin', 'petugas'])) {
            $borrowing = Borrowing::with(['users', 'books', 'bookitems', 'member'])
                ->orderBy('pinjam', 'desc')
                ->get();
        } else {
            // member atau non-admin hanya lihat peminjaman miliknya (jika dia juga member)
            $member = Members::where('id_user', Auth::id())->first();
            if ($member) {
                $borrowing = Borrowing::with(['users', 'books', 'bookitems', 'member'])
                    ->where('id_member', $member->id_member)
                    ->orderBy('pinjam', 'desc')
                    ->get();
            } else {
                $borrowing = collect();
            }
        }

        $books = Books::all();
        $users = User::all();
        $bookitems = Bookitems::where('status', 'tersedia')->get();

        return view('borrowing.index', compact('borrowing', 'books', 'users', 'bookitems'));
    }

    /**
     * STORE - oleh admin / petugas
     * Mendukung multi-buku (id_buku[] / id_item[]) tanpa tabel baru.
     * Akan membuat satu row per buku di table borrowing dalam 1 DB transaction.
     */
    public function store(Request $request)
    {
        // hanya admin/petugas boleh create dari backend modal
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'petugas'])) {
            abort(403, 'Akses ditolak!');
        }

        try {
            // Validasi input
            $request->validate([
                'id_user' => 'required|exists:users,id_user',
                'id_item' => 'required|array|min:1|max:2',
                'id_item.*' => 'required|exists:bookitems,id_item',
                'pengembalian' => 'required|date|after:today',
                'kondisi' => 'required|in:baik,rusak'
            ], [
                'id_item.required' => 'Pilih minimal 1 buku/eksemplar.',
                'id_item.max' => 'Maksimal hanya bisa meminjam 2 buku sekaligus.',
                'pengembalian.after' => 'Tanggal pengembalian harus setelah hari ini.',
            ]);

            $idsItem = $request->input('id_item');

            // Get member data
            $member = Members::where('id_user', $request->input('id_user'))->first();

            if (!$member) {
                return back()->with('error', 'Member tidak ditemukan untuk user yang dipilih.');
            }

            if ($member->status !== 'verified') {
                return back()->with('error', 'Member belum diverifikasi. Hubungi admin untuk verifikasi akun.');
            }

            // ✅ VALIDASI LIMIT PEMINJAMAN YANG LEBIH KETAT
            $activeBorrowCount = Borrowing::where('id_member', $member->id_member)
                ->whereIn('status', ['Dipinjam', 'dipinjam', 'pending'])
                ->count();

            $requestedCount = count($idsItem);
            $totalAfterBorrow = $activeBorrowCount + $requestedCount;

            if ($totalAfterBorrow > 2) {
                return back()->with('error',
                    "❌ Member ini sudah meminjam {$activeBorrowCount} buku aktif. " .
                    "Tidak bisa menambah {$requestedCount} buku lagi. " .
                    "Maksimal peminjaman adalah 2 buku aktif."
                );
            }

            // Validasi setiap eksemplar
            foreach ($idsItem as $itemId) {
                $item = Bookitems::find($itemId);
                if (!$item) {
                    return back()->with('error', "Eksemplar dengan ID {$itemId} tidak ditemukan.");
                }
                if ($item->status !== 'tersedia') {
                    return back()->with('error', "Eksemplar {$item->kode_item} sedang tidak tersedia.");
                }
            }

            // ========================================
            // GENERATE TRANSACTION ID YANG UNIQUE
            // ========================================
            $today = date('Ymd');
            $randomSuffix = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
            $transactionId = "TRX{$today}-{$randomSuffix}";

            // ========================================
            // PROSES SEMUA BUKU DALAM 1 TRANSAKSI
            // ========================================
            $savedCount = 0;
            $bookTitles = [];

            DB::transaction(function () use ($idsItem, $request, $member, $transactionId, &$savedCount, &$bookTitles) {

                $pinjamDate = now();
                $pengembalianDate = Carbon::parse($request->input('pengembalian'));

                // Validasi tanggal pengembalian
                if ($pengembalianDate->diffInDays($pinjamDate) > 14) {
                    throw new \Exception("Maksimal peminjaman adalah 14 hari.");
                }

                foreach ($idsItem as $itemId) {
                    $chosenItem = Bookitems::with('books')->find($itemId);

                    if (!$chosenItem) {
                        throw new \Exception("Eksemplar dengan id_item {$itemId} tidak ditemukan.");
                    }

                    if ($chosenItem->status !== 'tersedia') {
                        throw new \Exception("Eksemplar {$chosenItem->kode_item} tidak tersedia.");
                    }

                    // ✅ SEMUA BUKU PAKAI TRANSACTION_ID, TANGGAL PINJAM, DAN PENGEMBALIAN YANG SAMA
                    Borrowing::create([
                        'transaction_id' => $transactionId,
                        'id_user' => $request->input('id_user'),
                        'id_member' => $member->id_member,
                        'id_buku' => $chosenItem->id_buku,
                        'id_item' => $chosenItem->id_item,
                        'kondisi' => $request->input('kondisi', 'baik'),
                        'status' => 'Dipinjam',
                        'alamat_peminjam' => $member->alamat,
                        'pinjam' => $pinjamDate,
                        'pengembalian' => $pengembalianDate,
                        'is_extended' => false,
                    ]);

                    $savedCount++;
                    $bookTitles[] = $chosenItem->books->judul ?? 'Unknown';

                    // Update status item & stok buku
                    $chosenItem->update(['status' => 'dipinjam']);
                    Books::where('id_buku', $chosenItem->id_buku)->decrement('jumlah');
                }
            });

            Log::info('Peminjaman berhasil dibuat', [
                'transaction_id' => $transactionId,
                'user_id' => $request->input('id_user'),
                'member_id' => $member->id_member,
                'member_name' => $member->name,
                'count' => $savedCount,
                'books' => $bookTitles,
                'created_by' => Auth::user()->name,
            ]);

            $bookList = implode(', ', array_slice($bookTitles, 0, 2));
            if (count($bookTitles) > 2) {
                $bookList .= ', dll';
            }

            return back()->with('success',
                "✅ Peminjaman berhasil dibuat! " .
                "{$savedCount} buku dipinjam ({$bookList}). " .
                "Transaction ID: {$transactionId}"
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error store borrowing: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal menyimpan peminjaman: ' . $e->getMessage());
        }
    }

    /**
     * PEMINJAMAN DARI USER (borrow) - user-initiated (frontend)
     * Mendukung juga input single atau array; limit dan validasi sama.
     */
    public function borrow(Request $request)
    {
        try {
            // allow single or multiple; we'll validate later
            $idsBuku = $request->input('id_buku');
            $idsItem = $request->input('id_item');

            $idsBuku = is_array($idsBuku) ? $idsBuku : ($idsBuku ? [$idsBuku] : []);
            $idsItem = is_array($idsItem) ? $idsItem : ($idsItem ? [$idsItem] : []);

            $member = Members::where('id_user', Auth::id())->first();

            if (!$member) {
                return back()->with('error', 'Belum terdaftar sebagai member.');
            }

            if ($member->status !== 'verified') {
                return back()->with('error', 'Akun member belum diverifikasi.');
            }

            $activeBorrowCount = Borrowing::where('id_member', $member->id_member)
                ->where('status', 'Dipinjam')
                ->count();

            $requestedCount = max(count($idsBuku), count($idsItem));
            if ($activeBorrowCount + $requestedCount > 2) {
                return back()->with('error', 'Limit peminjaman (2 buku aktif) sudah tercapai.');
            }

            DB::transaction(function () use ($idsBuku, $idsItem, $request, $member) {
                $totalToProcess = max(count($idsBuku), count($idsItem));

                for ($i = 0; $i < $totalToProcess; $i++) {
                    $bookId = $idsBuku[$i] ?? null;
                    $itemId = $idsItem[$i] ?? null;
                    $chosenItem = null;

                    if ($itemId) {
                        $chosenItem = Bookitems::find($itemId);
                        if (!$chosenItem) {
                            throw new \Exception("Eksemplar tidak ditemukan.");
                        }
                    } elseif ($bookId) {
                        $chosenItem = Bookitems::where('id_buku', $bookId)
                            ->where('status', 'tersedia')
                            ->first();
                        if (!$chosenItem) {
                            throw new \Exception("Tidak ada eksemplar tersedia untuk buku ID {$bookId}.");
                        }
                    } else {
                        continue;
                    }

                    if ($chosenItem->status !== 'tersedia') {
                        throw new \Exception("Item buku sedang tidak tersedia.");
                    }

                    if ($chosenItem->kondisi !== 'baik') {
                        throw new \Exception("Item buku tidak dalam kondisi baik.");
                    }

                    Borrowing::create([
                        'id_user' => Auth::id(),
                        'id_member' => $member->id_member,
                        'id_buku' => $chosenItem->id_buku,
                        'id_item' => $chosenItem->id_item,
                        'pinjam' => now(),
                        'pengembalian' => $request->input('pengembalian', now()->addDays(7)),
                        'status' => 'Dipinjam',
                        'kondisi' => 'baik',
                        'alamat_peminjam' => $member->alamat,
                        'is_extended' => false,
                    ]);

                    $chosenItem->update(['status' => 'dipinjam']);
                    Books::where('id_buku', $chosenItem->id_buku)->decrement('jumlah');
                }
            });

            return redirect()->route('borrowing.index')->with('success', '✅ Buku berhasil dipinjam!');

        } catch (\Exception $e) {
            Log::error('Error borrow: ' . $e->getMessage());
            return back()->with('error', 'Gagal meminjam buku: ' . $e->getMessage());
        }
    }

    /**
     * PERPANJANG - hanya admin/petugas
     */
    public function perpanjang($id)
    {
        // guard role
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'petugas'])) {
            // Return JSON if it's an AJAX request
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak!'], 403);
            }
            abort(403, 'Akses ditolak!');
        }

        try {
            $borrowing = Borrowing::with(['books'])->findOrFail($id);

            if (!in_array($borrowing->status, ['Dipinjam', 'dipinjam'])) {
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Buku tidak sedang dipinjam.']);
                }
                return back()->with('error', 'Buku tidak sedang dipinjam.');
            }

            if ($borrowing->is_extended) {
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Buku sudah pernah diperpanjang.']);
                }
                return back()->with('error', 'Buku sudah pernah diperpanjang.');
            }

            if (Carbon::now()->gt($borrowing->pengembalian)) {
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Tidak bisa perpanjang, buku sudah terlambat dikembalikan!']);
                }
                return back()->with('error', 'Tidak bisa perpanjang, buku sudah terlambat dikembalikan!');
            }

            $newDate = Carbon::parse($borrowing->pengembalian)->addDays(7);

            $borrowing->update([
                'pengembalian' => $newDate,
                'is_extended' => true,
            ]);

            Log::info('Peminjaman diperpanjang', [
                'id_peminjaman' => $id,
                'new_date' => $newDate,
                'by' => Auth::id()
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '📅 Buku "' . ($borrowing->books->judul ?? '-') . '" diperpanjang sampai ' . $newDate->format('d M Y')
                ]);
            }

            return back()->with('success', '📅 Buku "' . ($borrowing->books->judul ?? '-') . '" diperpanjang sampai ' . $newDate->format('d M Y'));

        } catch (\Exception $e) {
            Log::error('Error perpanjang: ' . $e->getMessage());

            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal memperpanjang: ' . $e->getMessage()]);
            }

            return back()->with('error', 'Gagal memperpanjang: ' . $e->getMessage());
        }
    }

    /**
     * KEMBALIKAN - hanya admin/petugas
     * Updated to handle both form data and JSON requests
     */
    public function kembalikan($id, Request $request)
    {
        // hanya admin/petugas boleh proses pengembalian
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'petugas'])) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak!'], 403);
            }
            abort(403, 'Akses ditolak!');
        }

        try {
            $borrowing = Borrowing::with(['bookitems', 'books'])->findOrFail($id);

            if (!in_array($borrowing->status, ['Dipinjam', 'dipinjam'])) {
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Buku ini sudah dikembalikan.']);
                }
                return back()->with('error', 'Buku ini sudah dikembalikan.');
            }

            // Get data from JSON or form
            $kondisi = $request->input('kondisi', $borrowing->kondisi);
            $denda = $request->input('denda');
            $catatan = $request->input('catatan');

            // Validate
            if (!in_array($kondisi, ['baik', 'rusak', 'hilang'])) {
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Kondisi tidak valid.']);
                }
                return back()->with('error', 'Kondisi tidak valid.');
            }

            DB::transaction(function () use ($borrowing, $kondisi, $denda, $catatan) {
                // update status peminjaman
                $borrowing->status = 'Dikembalikan';
                $borrowing->kondisi = $kondisi;
                $borrowing->catatan = $catatan;

                // simpan denda jika diberikan
                if ($denda !== null && $denda > 0) {
                    $borrowing->denda = $denda;
                }

                $borrowing->save();

                // update status item buku menjadi tersedia
                if ($borrowing->bookitems) {
                    $borrowing->bookitems->update(['status' => 'tersedia']);
                }

                // kembalikan stok buku (increment jumlah)
                if ($borrowing->books) {
                    $borrowing->books->increment('jumlah');
                }
            });

            Log::info('Buku dikembalikan', [
                'id_peminjaman' => $id,
                'denda' => $denda,
                'kondisi' => $kondisi,
                'by' => Auth::id(),
                'returned_at' => now()
            ]);

            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => '✅ Buku telah dikembalikan dan data disimpan.']);
            }

            return back()->with('success', '✅ Buku telah dikembalikan dan data disimpan.');

        } catch (\Exception $e) {
            Log::error('Error kembalikan: ' . $e->getMessage());

            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal mengembalikan buku: ' . $e->getMessage()]);
            }

            return back()->with('error', 'Gagal mengembalikan buku: ' . $e->getMessage());
        }
    }


    /**
     * ALIAS LAMA (backward compatible) - jangan hapus
     */
    public function update(Request $request, $id)
    {
        try {
            $borrowing = Borrowing::findOrFail($id);
            $borrowing->update($request->all());

            return back()->with('success', 'Data peminjaman berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function return($id)
    {
        return $this->kembalikan($id, request());
    }

    /**
     * HAPUS (soft delete) - tetap ada
     */
    public function destroy($id)
    {
        try {
            $borrowing = Borrowing::findOrFail($id);

            if (in_array($borrowing->status, ['Dipinjam', 'dipinjam'])) {
                if ($borrowing->bookitems) {
                    $borrowing->bookitems->update(['status' => 'tersedia']);
                }
                if ($borrowing->books) {
                    $borrowing->books->increment('jumlah');
                }
            }

            $borrowing->delete();

            return back()->with('success', 'Data peminjaman dihapus.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}
