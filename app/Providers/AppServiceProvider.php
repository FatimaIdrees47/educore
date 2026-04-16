<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Http\View\Composers\UnreadMessageComposer;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Blade::component('layouts.super-admin', 'layouts.super-admin');
        Blade::component('layouts.admin', 'layouts.admin');
        Blade::component('layouts.teacher', 'layouts.teacher');
        Blade::component('layouts.student', 'layouts.student');
        Blade::component('layouts.parent', 'layouts.parent');

        View::composer([
            'layouts.teacher',
            'layouts.parent',
        ], UnreadMessageComposer::class);
    }
}