<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Books;
use App\Models\Bookitems;
use App\Models\Members;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Halaman Daftar Denda
     */
    public function denda()
    {
        $borrowings = Borrowing::with(['users', 'member', 'books', 'bookitems'])
            ->where('denda', '>', 0)
            ->orWhereIn('status', ['Dipinjam', 'dipinjam'])
            ->whereDate('pengembalian', '<', now())
            ->orderBy('pengembalian', 'asc')
            ->get();

        // Hitung total denda
        $totalDenda = $borrowings->sum('denda');

        // Hitung denda yang belum dibayar (masih dipinjam + terlambat)
        $dendaBelumBayar = $borrowings
            ->filter(fn($b) => in_array($b->status, ['Dipinjam', 'dipinjam']) && $b->isLate())
            ->count();

        return view('laporan.denda', compact('borrowings', 'totalDenda', 'dendaBelumBayar'));
    }

    /**
     * Halaman Riwayat Transaksi (7 hari terakhir)
     */
    public function riwayat()
    {
        $sevenDaysAgo = now()->subDays(7);

        $borrowings = Borrowing::with(['users', 'member', 'books', 'bookitems'])
            ->where('pinjam', '>=', $sevenDaysAgo)
            ->orderBy('pinjam', 'desc')
            ->get();

        // Statistik
        $totalTransaksi = $borrowings->unique('id_peminjaman')->count();
        $totalBuku = $borrowings->count();
        $totalDikembalikan = $borrowings->filter(fn($b) => in_array($b->status, ['Dikembalikan', 'dikembalikan']))->count();
        $totalDipinjam = $borrowings->filter(fn($b) => in_array($b->status, ['Dipinjam', 'dipinjam']))->count();

        return view('laporan.riwayat', compact('borrowings', 'totalTransaksi', 'totalBuku', 'totalDikembalikan', 'totalDipinjam'));
    }

    /**
     * Halaman Keterlambatan
     */
    public function keterlambatan()
    {
        $borrowings = Borrowing::with(['users', 'member', 'books', 'bookitems'])
            ->whereIn('status', ['Dipinjam', 'dipinjam'])
            ->whereDate('pengembalian', '<', now())
            ->orderBy('pengembalian', 'asc')
            ->get();

        // Kategorisasi berdasarkan hari terlambat
        $terlambatRingan = $borrowings->filter(fn($b) => $b->getDaysLate() >= 1 && $b->getDaysLate() <= 3)->count();
        $terlambatSedang = $borrowings->filter(fn($b) => $b->getDaysLate() >= 4 && $b->getDaysLate() <= 7)->count();
        $terlambatBerat = $borrowings->filter(fn($b) => $b->getDaysLate() > 7)->count();

        return view('laporan.keterlambatan', compact('borrowings', 'terlambatRingan', 'terlambatSedang', 'terlambatBerat'));
    }

    /**
     * Halaman Buku Rusak
     */
    public function bukuRusak()
    {
        $borrowings = Borrowing::with(['users', 'member', 'books', 'bookitems'])
            ->whereIn('kondisi', ['rusak', 'hilang'])
            ->whereIn('status', ['Dikembalikan', 'dikembalikan'])
            ->orderBy('pinjam', 'desc')
            ->get();

        // Statistik
        $totalRusak = $borrowings->filter(fn($b) => $b->kondisi === 'rusak')->count();
        $totalHilang = $borrowings->filter(fn($b) => $b->kondisi === 'hilang')->count();
        $totalKerugian = $borrowings->sum('denda');

        return view('laporan.buku-rusak', compact('borrowings', 'totalRusak', 'totalHilang', 'totalKerugian'));
    }

    /**
     * Statistik untuk Dashboard
     */
    public function statistik(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        // Total peminjaman bulan ini
        $totalPeminjaman = Borrowing::whereMonth('pinjam', $bulan)
            ->whereYear('pinjam', $tahun)
            ->count();

        // Total dikembalikan
        $totalDikembalikan = Borrowing::whereMonth('pinjam', $bulan)
            ->whereYear('pinjam', $tahun)
            ->whereIn('status', ['Dikembalikan', 'dikembalikan'])
            ->count();

        // Total terlambat
        $totalTerlambat = Borrowing::whereMonth('pinjam', $bulan)
            ->whereYear('pinjam', $tahun)
            ->whereIn('status', ['Dipinjam', 'dipinjam'])
            ->whereDate('pengembalian', '<', now())
            ->count();

        // Total denda
        $totalDenda = Borrowing::whereMonth('pinjam', $bulan)
            ->whereYear('pinjam', $tahun)
            ->sum('denda');

        // Chart data - peminjaman per hari
        $chartData = Borrowing::whereMonth('pinjam', $bulan)
            ->whereYear('pinjam', $tahun)
            ->select(DB::raw('DATE(pinjam) as tanggal'), DB::raw('COUNT(*) as jumlah'))
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        return response()->json([
            'totalPeminjaman' => $totalPeminjaman,
            'totalDikembalikan' => $totalDikembalikan,
            'totalTerlambat' => $totalTerlambat,
            'totalDenda' => $totalDenda,
            'chartData' => $chartData,
        ]);
    }
}
