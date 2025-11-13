<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Members;

class MemberPolicy
{
    public function viewAny(User $user)
    {
        return in_array(strtolower($user->role), ['admin', 'petugas']);
    }

    public function view(User $user, Members $member)
    {
        return $user->id_user === $member->id_user || in_array(strtolower($user->role), ['admin', 'petugas']);
    }

    public function create(User $user)
    {
        return in_array(strtolower($user->role), ['admin', 'petugas']);
    }

    public function update(User $user, Members $member)
    {
        return in_array(strtolower($user->role), ['admin', 'petugas']) || $user->id_user === $member->id_user;
    }

    public function delete(User $user, Members $member)
    {
        return strtolower($user->role) === 'admin';
    }

    public function restore(User $user, Members $member)
    {
        return strtolower($user->role) === 'admin';
    }

    public function forceDelete(User $user, Members $member)
    {
        return strtolower($user->role) === 'admin';
    }

    public function toggleStatus(User $user, Members $member)
    {
        return in_array(strtolower($user->role), ['admin', 'petugas']);
    }

    public function viewBorrowHistory(User $user, Members $member)
    {
        return $user->id_user === $member->id_user || in_array(strtolower($user->role), ['admin', 'petugas']);
    }

    public function viewKtp(User $user, Members $member)
    {
        return strtolower($user->role) === 'admin';
    }

    public function verifyKtp(User $user, Members $member)
    {
        return strtolower($user->role) === 'admin';
    }
}
