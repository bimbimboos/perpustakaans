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

    Route::middleware(['auth'])->prefix('members')->name('members.')->group(function () {
        // Profile & Registration (Konsumen)
        Route::get('profile', [MemberController::class, 'profile'])->name('profile');
        Route::get('create', [MemberController::class, 'create'])->name('create');
        Route::post('/', [MemberController::class, 'store'])->name('store');

        // List & Management
        Route::get('/', [MemberController::class, 'index'])->name('index');
        Route::get('{id_member}', [MemberController::class, 'show'])->name('show');
        Route::get('{id_member}/edit', [MemberController::class, 'edit'])->name('edit');
        Route::put('{id_member}', [MemberController::class, 'update'])->name('update');
        Route::delete('{id_member}', [MemberController::class, 'destroy'])->name('destroy');

        // AJAX
        Route::get('{id_member}/edit-data', [MemberController::class, 'getEditData'])->name('edit-data');

        // Bulk Actions
        Route::post('bulk-delete', [MemberController::class, 'bulkDelete'])->name('bulk-delete');

        // File Downloads
        Route::get('{id_member}/download-ktp', [MemberController::class, 'downloadKtp'])->name('download-ktp');
        Route::get('{id_member}/download-photo', [MemberController::class, 'downloadPhoto'])->name('download-photo');

        // Verifikasi Member
        Route::get('{id_member}/verify', [MemberController::class, 'verify'])->name('verify');
        Route::post('{id_member}/verify-manual', [MemberController::class, 'verifyManual'])->name('verify-manual');
        Route::post('{id_member}/reject', [MemberController::class, 'reject'])->name('reject');
        Route::post('{id_member}/verify-ktp', [MemberController::class, 'verifyKtp'])->name('verify-ktp');

        // Borrowing History
        Route::get('{id_member}/borrowing', [MemberController::class, 'borrowing'])->name('borrowing');
    });


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
