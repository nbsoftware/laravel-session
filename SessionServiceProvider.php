<?php

namespace Brill\LaravelSession;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class SessionServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['session'] = function ($app) {
            return new SessionManager($app);
        };

        $app['session.store'] = function ($app) {
            $manager = $app['session'];

            return $manager->driver();
        };
    }
}