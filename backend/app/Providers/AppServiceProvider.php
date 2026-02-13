<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Gate::policy(\App\Domain\User\Models\User::class, \App\Policies\User\UserPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Domain\Wallet\Models\Wallet::class, \App\Policies\Wallet\WalletPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Domain\Transaction\Models\Transaction::class, \App\Policies\Transaction\TransactionPolicy::class);
    }
}
