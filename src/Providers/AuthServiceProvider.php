<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Providers;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use MyParcelCom\AuthModule\Interfaces\RequestAuthenticatorInterface;
use MyParcelCom\AuthModule\JwtRequestAuthenticator;
use MyParcelCom\AuthModule\Middleware\CheckForAnyScope;
use MyParcelCom\AuthModule\Middleware\CheckForScopes;
use MyParcelCom\AuthModule\Middleware\CheckIfTokenIsFresh;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->singleton(RequestAuthenticatorInterface::class, function (Container $app) {
            return $app->make(JwtRequestAuthenticator::class);
        });

        $this->app->singleton(CheckForAnyScope::class, function (Container $app) {
            return (new CheckForAnyScope())
                ->setAuthenticator($app->make(RequestAuthenticatorInterface::class));
        });

        $this->app->singleton(CheckForScopes::class, function (Container $app) {
            return (new CheckForScopes())
                ->setAuthenticator($app->make(RequestAuthenticatorInterface::class));
        });

        $this->app->singleton(CheckIfTokenIsFresh::class, function (Container $app) {
            return new CheckIfTokenIsFresh(
                $app->make(RequestAuthenticatorInterface::class),
                config('auth.token_freshness_threshold'),
            );
        });
    }
}
