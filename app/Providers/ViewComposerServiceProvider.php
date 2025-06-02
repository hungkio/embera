<?php

declare(strict_types=1);

namespace App\Providers;

use App\Composers\CurrentUserComposer;
use App\Composers\MenuComposer;
use App\Composers\PageComposer;
use App\Composers\TaxonComposer;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Factory;

class ViewComposerServiceProvider extends ServiceProvider
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
    public function boot(Factory $factory): void
    {
        $factory->composer('admin.*', CurrentUserComposer::class);
        $factory->composer('shop.*', MenuComposer::class);
    }
}
