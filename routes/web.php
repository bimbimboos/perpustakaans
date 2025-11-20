<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BukusController;
use App\Http\Controllers\BukuItemsController;
use App\Http\Controllers\RaksController;
use App\Http\Controllers\PenerbitsController;
use App\Http\Controllers\LokasiRaksController;
use App\Http\Controllers\KategorisController;
use App\Http\Controllers\SubKategorisController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PenataanBukusController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\Admin\MemberVerificationController;
use App\Http\Controllers\LaporanController;

// ==========================
// 🏠 ROUTE UTAMA
// ==========================
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

// ==========================
// 🧩 ROUTE TAMBAHAN (AJAX/API)
// ==========================
Route::get('/get-rak-by-buku/{id_buku}', [PenataanBukusController::class, 'getRakByBuku']);
Route::get('/racks/{id_rak}', [RaksController::class, 'show'])->name('racks.show');
Route::get('/get-books-for-selection', [BukusController::class, 'getForSelection']);

// ==========================
// 🔐 AUTH & DASHBOARD
// ==========================
require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return match (Auth::user()->role) {
            'admin' => view('dashboard.admin'),
            'petugas' => view('dashboard.petugas'),
            default => view('dashboard.konsumen'),
        };
    })->name('dashboard');
});

// ========================================
// 📘 PEMINJAMAN (SEMUA LOGIN)
// ========================================
Route::middleware(['auth'])->prefix('borrowing')->name('borrowing.')->group(function () {
    // READ
    Route::get('/', [PeminjamanController::class, 'index'])->name('index');

    // CREATE
    Route::post('/store', [PeminjamanController::class, 'store'])->name('store');
    Route::post('/borrow', [PeminjamanController::class, 'borrow'])->name('borrow');

    // UPDATE - Perpanjang & Kembalikan
    Route::post('/{id}/perpanjang', [PeminjamanController::class, 'perpanjang'])->name('perpanjang');
    Route::post('/{id}/kembali', [PeminjamanController::class, 'kembalikan'])->name('kembali');

    // BACKWARD COMPATIBILITY (JANGAN DIHAPUS DULU)
    Route::put('/{id}/update', [PeminjamanController::class, 'update'])->name('update');
    Route::post('/{id}/kembalikan', [PeminjamanController::class, 'return'])->name('kembalikan');

    // DELETE
    Route::delete('/{id}', [PeminjamanController::class, 'destroy'])->name('destroy');
});


// ==========================
// 📚 ADMIN, PETUGAS, KONSUMEN
// ==========================
Route::middleware(['auth', 'role:admin,petugas,konsumen'])->group(function () {
    Route::resource('books', BukusController::class);
    Route::resource('racks', RaksController::class);
    Route::resource('publisher', PenerbitsController::class);
    Route::resource('rackslocation', LokasiRaksController::class);
    Route::resource('categories', KategorisController::class);
    Route::resource('subcategories', SubKategorisController::class);
    Route::resource('books.items', BukuItemsController::class);
    Route::resource('sortbooks', PenataanBukusController::class);
});


// ==========================
// 👥 MEMBER ROUTES (FIXED)
// ==========================

// ==========================
// 👥 MEMBER ROUTES (OFFLINE REGISTRATION - FINAL)
// ==========================

// ==========================
// 👥 MEMBER ROUTES (OFFLINE REGISTRATION - FINAL)
// ==========================

Route::middleware(['auth'])->prefix('members')->name('members.')->group(function () {

    // List Member
    Route::get('/', function () {
        if (!in_array(strtolower(auth()->user()->role), ['admin', 'petugas'])) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }
        return app(\App\Http\Controllers\MemberController::class)->index(request());
    })->name('index');

    // Store
    Route::post('/', [\App\Http\Controllers\MemberController::class, 'store'])->name('store');

    // DOWNLOAD – harus di atas id_member
    Route::get('/{id_member}/download-ktp', function ($id_member) {
        if (!in_array(strtolower(auth()->user()->role), ['admin', 'petugas'])) {
            abort(403, 'Anda tidak memiliki akses');
        }
        return app(\App\Http\Controllers\MemberController::class)->downloadKtp($id_member);
    })->name('download-ktp');

    Route::get('/{id_member}/download-photo', function ($id_member) {
        if (!in_array(strtolower(auth()->user()->role), ['admin', 'petugas'])) {
            abort(403, 'Anda tidak memiliki akses');
        }
        return app(\App\Http\Controllers\MemberController::class)->downloadPhoto($id_member);
    })->name('download-photo');

    // PRINT CARD – Tambahkan route ini (harus sebelum {id_member})
    Route::get('/{id_member}/print-card', function ($id_member) {
        // Bisa diakses oleh admin, petugas, atau member itu sendiri
        $member = \App\Models\Members::findOrFail($id_member);

        // Cek authorization
        if (!in_array(strtolower(auth()->user()->role), ['admin', 'petugas'])
            && auth()->user()->id_user !== $member->id_user) {
            abort(403, 'Anda tidak memiliki akses');
        }

        return app(\App\Http\Controllers\MemberController::class)->printCard($id_member);
    })->name('print-card');

    // SHOW – pakai path lain biar ga tabrakan
    Route::get('/detail/{id_member}', function ($id_member) {
        if (!in_array(strtolower(auth()->user()->role), ['admin', 'petugas'])) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }
        return app(\App\Http\Controllers\MemberController::class)->show($id_member);
    })->name('show');

    // UPDATE – jangan pakai /{id_member} lagi
    Route::put('/update/{id_member}', [\App\Http\Controllers\MemberController::class, 'update'])->name('update');

    // DELETE – sama, path dibedain
    Route::delete('/delete/{id_member}', [\App\Http\Controllers\MemberController::class, 'destroy'])->name('destroy');

    // BULK DELETE
    Route::post('/bulk-delete', [\App\Http\Controllers\MemberController::class, 'bulkDelete'])->name('bulk-delete');
});


// 👤 Konsumen: View Own Profile ONLY
Route::middleware(['auth'])->get('/members/profile', [\App\Http\Controllers\MemberController::class, 'myProfile'])->name('members.profile');

// ==========================
// 👑 ADMIN SAJA
// ==========================
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::get('/users/{id}/download-ktp', [UserController::class, 'downloadKtp'])->name('users.downloadKtp');
});

// ==========================
// 🔔 NOTIFIKASI (SEMUA LOGIN)
// ==========================
Route::middleware(['auth'])
    ->prefix('notifications')
    ->name('notifications.')
    ->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('{id}', [NotificationController::class, 'destroy'])->name('destroy');
    });

// ==========================
// 👤 PROFIL (SEMUA LOGIN)
// ==========================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.update.photo');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// ==========================
// 📊 LAPORAN (ADMIN & PETUGAS)
// ==========================
Route::middleware(['auth', 'role:admin,petugas'])->prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/denda', [App\Http\Controllers\LaporanController::class, 'denda'])->name('denda');
    Route::get('/riwayat', [App\Http\Controllers\LaporanController::class, 'riwayat'])->name('riwayat');
    Route::get('/keterlambatan', [App\Http\Controllers\LaporanController::class, 'keterlambatan'])->name('keterlambatan');
    Route::get('/buku-rusak', [App\Http\Controllers\LaporanController::class, 'bukuRusak'])->name('buku-rusak');
    Route::get('/statistik', [App\Http\Controllers\LaporanController::class, 'statistik'])->name('statistik');
});
