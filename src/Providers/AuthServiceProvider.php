<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Providers;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use MyParcelCom\AuthModule\Interfaces\TokenAuthenticatorInterface;
use MyParcelCom\AuthModule\JwtAuthenticator;
use MyParcelCom\AuthModule\Middleware\CheckForAnyScope;
use MyParcelCom\AuthModule\Middleware\CheckForScopes;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(TokenAuthenticatorInterface::class, function (Container $app) {
            return $app->make(JwtAuthenticator::class);
        });

        $this->app->singleton(CheckForAnyScope::class, function (Container $app) {
            return (new CheckForAnyScope())
                ->setAuthenticator($app->make(TokenAuthenticatorInterface::class));
        });

        $this->app->singleton(CheckForScopes::class, function (Container $app) {
            return (new CheckForScopes())
                ->setAuthenticator($app->make(TokenAuthenticatorInterface::class));
        });
    }
}