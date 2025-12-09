<?php

namespace Jhonoryza\Ipwhitelist;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '../config/ipwhitelist.php' => config_path('ipwhitelist.php'),
        ], 'ipwhitelist-config');

        $this->app['router']->aliasMiddleware('ipwhitelist', WhitelistIpMiddleware::class);
    }
}
