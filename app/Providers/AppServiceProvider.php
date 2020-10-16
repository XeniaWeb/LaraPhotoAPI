<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services;

class AppServiceProvider extends ServiceProvider
{
    public $singletons = [
        v1\PhotoService::class => v1\PhotoService::class,
        v1\AlbumService::class => v1\AlbumService::class
    ];

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
    public function boot()
    {
        //
    }
}
