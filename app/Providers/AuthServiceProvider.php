<?php

namespace App\Providers;

use App\Models\Entrepreneur;
use App\Policies\EntrepreneurPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * 策略映射
     */
    protected $policies = [
        Entrepreneur::class => EntrepreneurPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
