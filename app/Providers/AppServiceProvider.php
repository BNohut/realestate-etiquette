<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Orchid\Platform\Dashboard;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dashboard $dashboard)
    {
        $dashboard->registerResource('scripts', "//code.jquery.com/jquery-3.7.0.js");
        $dashboard->registerResource('scripts', "/js/modal_close_button.js");

        Blade::if('checkAccess', function (string $permission, $id = null) {

            return checkAccess($permission, $id);
        });
    }
}
