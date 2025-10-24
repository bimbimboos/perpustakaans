<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
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
use App\Http\Controllers\MemberController;
use App\Http\Controllers\NotificationController;

// ==========================
// ROUTE UMUM
// ==========================
Route::get('/', function () {
    return view('auth.login');
});

// ==========================
// ROUTE TAMBAHAN (AJAX/API)
// ==========================
Route::get('/get-rak-by-buku/{id_buku}', [PenataanBukusController::class, 'getRakByBuku']);
Route::get('/racks/{id_rak}', [RaksController::class, 'show'])->name('racks.show');
Route::get('/get-books-for-selection', [BukusController::class, 'getForSelection']);

// ==========================
// AUTH & DASHBOARD
// ==========================
require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $role = Auth::user()->role;

        if ($role === 'admin') {
            return view('dashboard.admin');
        } elseif ($role === 'petugas') {
            return view('dashboard.petugas');
        } else {
            return view('dashboard.konsumen');
        }
    })->name('dashboard');
});

// ==========================
// 📗 ROUTE PEMINJAMAN (SEMUA USER LOGIN)
// ==========================
Route::middleware(['auth'])->group(function () {
    Route::get('/borrowing', [PeminjamanController::class, 'index'])->name('borrowing.index');
    Route::get('/borrowing/notif', [PeminjamanController::class, 'notif'])->name('borrowing.notif');
    Route::post('/borrowing/store', [PeminjamanController::class, 'store'])->name('borrowing.store');
    Route::post('/borrowing/{id}/approve', [PeminjamanController::class, 'approve'])->name('borrowing.approve');
    Route::post('/borrowing/{id}/reject', [PeminjamanController::class, 'reject'])->name('borrowing.reject');
    Route::post('/borrowing/{id}/kembalikan', [PeminjamanController::class, 'return'])->name('borrowing.kembalikan');
    Route::put('/borrowing/{id}/update', [PeminjamanController::class, 'update'])->name('borrowing.update');

    // Pinjam dari halaman buku
    Route::post('/books/{id}/pinjam', [BukuItemsController::class, 'pinjam'])->name('books.pinjam');
});

// ==========================
// 👥 ROUTE MEMBERS (SEMUA USER LOGIN)
// ==========================
Route::middleware(['auth'])->prefix('members')->name('members.')->group(function () {
    // Resource routes (CRUD)
    Route::resource('/', MemberController::class)->parameters(['' => 'members']);

    // AJAX - Get edit data
    Route::get('{members}/edit-data', [MemberController::class, 'getEditData'])->name('edit-data');

    // Bulk delete
    Route::post('bulk-delete', [MemberController::class, 'bulkDelete'])->name('bulk-delete');

    // File downloads
    Route::get('{members}/download-ktp', [MemberController::class, 'downloadKtp'])->name('download-ktp');
    Route::get('{members}/download-photo', [MemberController::class, 'downloadPhoto'])->name('download-photo');

    // Verify KTP (admin only)
    Route::post('{members}/verify-ktp', [MemberController::class, 'verifyKtp'])
        ->name('verify-ktp')
        ->middleware('can:admin');
});

// ==========================
// ADMIN & PETUGAS
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
// ADMIN SAJA
// ==========================
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::get('/users/{id}/download-ktp', [UserController::class, 'downloadKtp'])
        ->name('users.downloadKtp');
});

// ==========================
// 🔔 ROUTE NOTIFICATIONS (ADMIN & OPERATOR)
// ==========================
Route::middleware(['auth'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::post('{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
    Route::post('mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    Route::delete('{id}', [NotificationController::class, 'destroy'])->name('destroy');
});

// ==========================
// ROUTE PROFIL (SEMUA YANG LOGIN)
// ==========================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.update.photo');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
