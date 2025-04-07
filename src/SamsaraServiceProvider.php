<?php

namespace ErikGall\Samsara;

use Illuminate\Support\ServiceProvider;

class SamsaraServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register services.
     */
    public function register(): void
    {

        $this->mergeConfigFrom(
            __DIR__.'/../config/services.php', 'services'
        );
    }
}
