<?php

namespace App\Providers;

use App\Discord\Interaction;
use App\Http\Controllers\Controller;
use Illuminate\Support\ServiceProvider;

class InteractionProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->scoped(Interaction::class, function ($app) {
            return new Interaction(request()->all());
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
//        $this->app->when('App\Http\Controllers\Controller')
//            ->needs('App\Discord\Interaction')
//            ->give(fn () => new Interaction(request()->all()));
    }
}
