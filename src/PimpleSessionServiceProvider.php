<?php

namespace Ackee\LaravelSession;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Illuminate\Session\SessionManager;

class PimpleSessionServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['session'] = function ($container) {
            return new SessionManager($container);
        };

        $container['session.store'] = function ($container) {
            $manager = $container['session'];

            return $manager->driver();
        };
    }
}