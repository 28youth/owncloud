<?php

declare(strict_types=1);

namespace Fisher\SSO\Providers;

use Illuminate\Support\ServiceProvider;
use App\Support\ManageRepository;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(
            $this->app->make('path.sso').'/router.php'
        );
    }
}
