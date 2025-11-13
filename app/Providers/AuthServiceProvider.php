<?php

namespace App\Providers;

use App\Models\Members;
use App\Models\User;
use App\Policies\MemberPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

/**
 * ═══════════════════════════════════════════════════════════════
 * AUTH SERVICE PROVIDER - COMPLETE VERSION
 * ═══════════════════════════════════════════════════════════════
 *
 * Register semua policies, gates, dan authorization rules
 *
 * Author: Bima Novascottia
 * Date: 27 Oktober 2025
 */

class AuthServiceProvider extends ServiceProvider
{
    /**
     * ═══════════════════════════════════════════════════════════
     * POLICY MAPPINGS
     * ═══════════════════════════════════════════════════════════
     *
     * Map model dengan policy-nya
     * Format: Model::class => Policy::class
     */
    protected $policies = [
        // ✅ MEMBER POLICY - INI YANG FIX ERROR 403!
        Members::class => MemberPolicy::class,

        // ✅ Kalau ada policy lain, tambahin di bawah ini:
        // User::class => UserPolicy::class,
        // \App\Models\Borrowing::class => \App\Policies\BorrowingPolicy::class,
        // \App\Models\Bukus::class => \App\Policies\BukuPolicy::class,
    ];

    /**
     * ═══════════════════════════════════════════════════════════
     * BOOT METHOD
     * ═══════════════════════════════════════════════════════════
     *
     * Register policies & gates
     */
    public function boot(): void
    {
        // ✅ REGISTER SEMUA POLICIES
        $this->registerPolicies();

        // ═══════════════════════════════════════════════════════
        // CUSTOM GATES - Role Based
        // ═══════════════════════════════════════════════════════

        /**
         * Gate: is-admin
         * Cek apakah user adalah admin
         * Usage: Gate::allows('is-admin') atau @can('is-admin')
         */
        Gate::define('is-admin', function (User $user) {
            return strtolower($user->role) === 'admin';
        });

        /**
         * Gate: is-petugas
         * Cek apakah user adalah petugas
         */
        Gate::define('is-petugas', function (User $user) {
            return strtolower($user->role) === 'petugas';
        });

        /**
         * Gate: is-konsumen
         * Cek apakah user adalah konsumen
         */
        Gate::define('is-konsumen', function (User $user) {
            return strtolower($user->role) === 'konsumen';
        });

        /**
         * Gate: is-staff
         * Cek apakah user adalah staff (admin atau petugas)
         */
        Gate::define('is-staff', function (User $user) {
            $role = strtolower($user->role);
            return $role === 'admin' || $role === 'petugas';
        });

        // ═══════════════════════════════════════════════════════
        // CUSTOM GATES - Permission Based
        // ═══════════════════════════════════════════════════════

        /**
         * Gate: manage-settings
         * Kelola pengaturan sistem (admin only)
         */
        Gate::define('manage-settings', function (User $user) {
            return strtolower($user->role) === 'admin';
        });

        /**
         * Gate: manage-users
         * Kelola user (admin only)
         */
        Gate::define('manage-users', function (User $user) {
            return strtolower($user->role) === 'admin';
        });

        /**
         * Gate: verify-members
         * Verifikasi member (admin & petugas)
         */
        Gate::define('verify-members', function (User $user) {
            $role = strtolower($user->role);
            return $role === 'admin' || $role === 'petugas';
        });

        /**
         * Gate: manage-books
         * Kelola buku (admin & petugas)
         */
        Gate::define('manage-books', function (User $user) {
            $role = strtolower($user->role);
            return $role === 'admin' || $role === 'petugas';
        });

        /**
         * Gate: manage-borrowing
         * Kelola peminjaman (admin & petugas)
         */
        Gate::define('manage-borrowing', function (User $user) {
            $role = strtolower($user->role);
            return $role === 'admin' || $role === 'petugas';
        });

        /**
         * Gate: approve-borrowing
         * Approve peminjaman (admin & petugas)
         */
        Gate::define('approve-borrowing', function (User $user) {
            $role = strtolower($user->role);
            return $role === 'admin' || $role === 'petugas';
        });

        /**
         * Gate: view-reports
         * Lihat laporan (admin & petugas)
         */
        Gate::define('view-reports', function (User $user) {
            $role = strtolower($user->role);
            return $role === 'admin' || $role === 'petugas';
        });

        /**
         * Gate: delete-any
         * Hapus data apapun (admin only)
         */
        Gate::define('delete-any', function (User $user) {
            return strtolower($user->role) === 'admin';
        });

        // ═══════════════════════════════════════════════════════
        // SUPER ADMIN GATE (Optional)
        // ═══════════════════════════════════════════════════════

        /**
         * Gate: before (Global)
         * Super admin bypass semua gate & policy
         * Uncomment jika mau pakai super admin
         */
        Gate::before(function (User $user, string $ability) {
            // ✅ SUPER ADMIN BYPASS
            // Ganti email sesuai super admin kamu
            if ($user->email === 'admin@perpustakaan.com') {
                return true;
            }

            // ✅ ADMIN BYPASS untuk semua action
            if (strtolower($user->role) === 'admin') {
                return true;
            }

            // Return null = lanjut cek gate/policy spesifik
            return null;
        });

        // ═══════════════════════════════════════════════════════
        // RESOURCE GATES (Optional - untuk resource controller)
        // ═══════════════════════════════════════════════════════

        /**
         * Gate: viewAny-members
         * Lihat list members
         */
        Gate::define('viewAny-members', function (User $user) {
            return in_array(strtolower($user->role), ['admin', 'petugas', 'konsumen']);
        });

        /**
         * Gate: create-members
         * Buat member baru
         */
        Gate::define('create-members', function (User $user) {
            return in_array(strtolower($user->role), ['admin', 'konsumen']);
        });
    }

    /**
     * ═══════════════════════════════════════════════════════════
     * REGISTER METHOD (Optional - kalau mau custom)
     * ═══════════════════════════════════════════════════════════
     */
    public function register(): void
    {
        // Register any custom services here
    }
}
