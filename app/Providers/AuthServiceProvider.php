<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
    }

    /**
     * Boot the authentication services for the application.
     */
    public function boot()
    {
        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->headers->get('authorization')) {
                $user = User::where('token', $request->headers->get('authorization'))->first();

                if ($user) {
                    return $user;
                }
            }
        });
    }
}
