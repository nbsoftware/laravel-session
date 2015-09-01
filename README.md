# Laravel Session middleware for SlimPHP

This middleware allows you to use Laravel Session library with Slim 3.
The benefit of this is being able to use different Session stores with the same API.

## How to use

```php

use Slim\Http\Request;
use Slim\Http\Response;

$app = new Slim\App();

// Determine our base path
$path = __DIR__;

$container = $app->getContainer();

$container['files'] = function () {
    return new Illuminate\Filesystem\Filesystem();
};

$container['config']['session.lifetime'] = 120; // Minutes idleable
$container['config']['session.expire_on_close'] = false;
$container['config']['session.lottery'] = array(2, 100); // lottery--how often do they sweep storage location to clear old ones?
$container['config']['session.cookie'] = 'laravel_session';
$container['config']['session.path'] = '/';
$container['config']['session.domain'] = null;
$container['config']['session.driver'] = 'file';
$container['config']['session.files'] = $path . '/sessions';

$app->getContainer()->register(new Ackee\LaravelSession\PimpleSessionServiceProvider($conatiner));
$app->add(new Ackee\LaravelSession\Middleware($container['session']));

$app->get('/', function (Request $request, Response $response, $args) {
    $this->session->set('test', 'This is my session data');
    return $response->write('Session set.');
});

$app->get('/test', function (Request $request, Response $response, $args) {
    $test = $this->session->get('test');
    return $res->write($test);
});

$app->run();
