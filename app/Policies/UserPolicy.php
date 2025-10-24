<?php

namespace App\Policies;

use App\Models\Members;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MemberPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if user can view any members
     */
    public function viewAny(?User $user): bool
    {
        // Hanya authenticated user
        return $user !== null;
    }

    /**
     * Determine if user can view the member
     */
    public function view(?User $user, Members $member): bool
    {
        // Admin atau member sendiri
        return $user && ($user->role === 'admin' || $user->id_user === $member->id_user);
    }

    /**
     * Determine if user can create members
     *
     * Revised:
     * - Jika config('library.allow_public_registration') === true => izinkan publik (guest) mendaftar
     * - Jika tidak, izinkan user yang role-nya 'admin' atau 'konsumen' (case-insensitive)
     */
    public function create(?User $user): bool
    {
        // Jika registrasi publik diaktifkan, buka untuk siapa saja (termasuk guest)
        if (config('library.allow_public_registration')) {
            return true;
        }

        // Authenticated users with role 'admin' or 'konsumen' boleh membuat member
        if ($user === null) {
            return false;
        }

        $role = strtolower($user->role ?? '');

        return in_array($role, ['admin', 'konsumen'], true);
    }

    /**
     * Determine if user can update the member
     */
    public function update(?User $user, Members $member): bool
    {
        // Admin atau owner
        return $user && ($user->role === 'admin' || $user->id_user === $member->id_user);
    }

    /**
     * Determine if user can delete the member
     */
    public function delete(?User $user, Members $member): bool
    {
        // Hanya admin yang bisa delete
        return $user?->role === 'admin';
    }

    /**
     * Determine if user can restore the member
     */
    public function restore(?User $user, Members $member): bool
    {
        return $user?->role === 'admin';
    }

    /**
     * Determine if user can permanently delete the member
     */
    public function forceDelete(?User $user, Members $member): bool
    {
        return $user?->role === 'admin';
    }

    /**
     * Determine if user can view KTP (CRITICAL: admin only!)
     */
    public function viewKtp(?User $user, Members $member): bool
    {
        // HANYA admin yang bisa lihat KTP
        return $user?->role === 'admin';
    }

    /**
     * Determine if user can verify KTP
     */
    public function verifyKtp(?User $user, Members $member): bool
    {
        return $user?->role === 'admin';
    }
}
