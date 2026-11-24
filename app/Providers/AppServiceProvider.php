<?php

namespace App\Providers;

use App\Services\Commit\Contracts\CommitApiClient;
use App\Services\Commit\HttpCommitApiClient;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CommitApiClient::class, HttpCommitApiClient::class);
        $this->app->bind(
            \App\Repositories\Reports\TransferReportRepositoryInterface::class,
            \App\Repositories\Reports\TransferReportRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Reports\SupplyReportRepositoryInterface::class,
            \App\Repositories\Reports\SupplyReportRepository::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

    }
}
