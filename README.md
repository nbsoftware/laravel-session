# Laravel Session middleware for SlimPHP

This middleware allows you to use Laravel Session library with Slim 3.
The benefit of this is being able to use different Session stores with the same API.

## Install

Via Composer

``` bash
composer require ackee/laravel-session
```

## How to use

```php

use Slim\Http\Request;
use Slim\Http\Response;

$app = new Slim\App();

$container = $app->getContainer();

// This is needed for file based session driver
$container['files'] = function () {
    return new Illuminate\Filesystem\Filesystem();
};

// This is needed to load session configuration because of the dot notation
$container['config'] = function ($c) {
    $loader = new Illuminate\Config\FileLoader($c['files'], __DIR__);
    return new Illuminate\Config\Repository($loader, 'production');
};

// These are the configs or you could create this in a external file 
// by using a proper config directory path in the loader above
$container['config']['session.lifetime'] = 120; // Minutes idleable
$container['config']['session.expire_on_close'] = false;
$container['config']['session.lottery'] = array(2, 100); // lottery--how often do they sweep storage location to clear old ones?
$container['config']['session.cookie'] = 'laravel_session';
$container['config']['session.path'] = '/';
$container['config']['session.domain'] = null;
$container['config']['session.driver'] = 'file';
$container['config']['session.files'] = __DIR__ . '/sessions';

$container->register(new Ackee\LaravelSession\PimpleSessionServiceProvider);
$app->add(new Ackee\LaravelSession\Middleware($container->get('session')));

$app->get('/', function ($request, $response, $args) {
    $this->session->set('test', 'This is my session data');
    return $response->write('Session set.');
});

$app->get('/test', function ($request, $response, $args) {
    $test = $this->session->get('test');
    return $response->write($test);
});

$app->run();
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
