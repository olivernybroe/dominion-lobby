<?php


namespace App\Providers;


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class MigrateServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blueprint::macro('userstamps', function () {
            $this->unsignedInteger('created_by')->nullable()->default(null);
            $this->unsignedInteger('updated_by')->nullable()->default(null);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}