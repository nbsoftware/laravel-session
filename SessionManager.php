<?php

namespace Brill\LaravelSession;

use Illuminate\Session\SessionManager as LaravelSessionManager;
use Slim\Interfaces\SessionInterface;

class SessionManager extends LaravelSessionManager implements SessionInterface {

    public function start()
    {
        // Session starts in the middleware
    }

    public function save()
    {
        // Session saves in the middleware
    }
}