# Laravel Session middleware for SlimPHP

This middleware allows you to use Laravel Session library instead of Slim's built in Session library.
The benefit of this is being able to use different Session stores with the same API.

## How to use

```php
$app = new Slim\App();
$app->register(new Brill\LaravelSession\SessionServiceProvider($app));
$app->add(new Brill\LaravelSession\Middleware($app['session']));
$app->run();
