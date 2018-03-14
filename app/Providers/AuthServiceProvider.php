<?php

namespace App\Providers;

use App\Services\JavaBogAuth\JavaBogGuard;
use App\Services\JavaBogAuth\JavaBogUserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Bridge\UserRepository;
use Laravel\Passport\Passport;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        Auth::extend('javaBogGuard', function ($app, $name, array $config) {
            return new JavaBogGuard(
                Auth::createUserProvider($config['provider']),
                $app['request']
            );
        });

        $this->app->bind(
            UserRepositoryInterface::class,
            JavaBogUserRepository::class
        );
        $this->app->bind(
            UserRepository::class,
            JavaBogUserRepository::class
        );
    }
}
