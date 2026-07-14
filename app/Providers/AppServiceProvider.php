<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\VersioningServiceInterface;
use App\Services\Versioning\VersioningService;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(VersioningServiceInterface::class, VersioningService::class);
    }

    public function boot(): void {}
}
