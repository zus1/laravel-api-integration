<?php

namespace Zus1\Api\Providers;

use Illuminate\Support\ServiceProvider;

class ApiIntegrationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/api-integration.php' => config_path('api-integration.php'),
        ]);
    }
}