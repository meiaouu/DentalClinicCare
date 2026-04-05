<?php

namespace App\Providers;

use App\Models\ClinicSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $view->with('globalClinic', ClinicSetting::first());
        });
    }
}
