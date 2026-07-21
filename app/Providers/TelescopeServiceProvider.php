<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\EntryType;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        $isLocal = $this->app->environment('local');

        Telescope::filter(function (IncomingEntry $entry) use ($isLocal) {
            if ($isLocal) {
                return true;
            }

            // Capture auth/session failures (401 "Token expired", 419 CSRF,
            // 403, 429) in addition to server errors. Telescope's built-in
            // isFailedRequest() only matches status >= 500, so these would
            // otherwise be dropped — and they are exactly what we're chasing.
            if ($entry->type === EntryType::REQUEST) {
                $status = $entry->content['response_status'] ?? 200;

                if ($status >= 500 || in_array($status, [401, 403, 419, 429], true)) {
                    return true;
                }
            }

            return $entry->isReportableException() ||
                   $entry->isFailedRequest() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function (?User $user = null) {
            // Open access: anyone can view the Telescope dashboard.
            // NOTE: this exposes requests, headers and tokens to anyone who
            // knows the /telescope URL. Restrict this list if that becomes a
            // concern on live stores.
            return true;
        });
    }
}
