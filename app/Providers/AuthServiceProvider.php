<?php
// app/Providers/AuthServiceProvider.php

namespace App\Providers;

use App\Models\Members;
use App\Policies\MemberPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Members::class => MemberPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Optional: Define gates
        Gates::define('admin', function ($user) {
            return $user->role === 'admin';
        });
    }
}
