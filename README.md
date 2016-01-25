# Laravel Session middleware for SlimPHP

This middleware allows you to use Laravel 5.x Session library with Slim 3.
The benefit of this is being able to use different Session stores with the same API.

## Install

Via Composer

``` bash
composer require ackee/laravel-session
```

## How to use

```php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Http\Request;
use Slim\Http\Response;

$settings = [
    'settings' => [
        // display detailed error in browser
        'displayErrorDetails' => true,

        'connections' => [
            'conn1' => [
                'driver'   => 'pgsql',
                'host'     => 'localhost',
                'database' => 'tests',
                'username' => 'postgres',
                'password' => 'xxx',
                'charset'  => 'utf8',
                'prefix'   => '',
                'schema'   => 'public',
            ],
        ],
    ],
];

$app = new Slim\App();
$container = $app->getContainer();
$sessionContainer = new \Illuminate\Container\Container();

// Here are two usage examples:
// One for file based and one for database based session driver
// Use either one of them and comment the other one

// This is needed for file based session driver
//$sessionContainer['files'] = new Illuminate\Filesystem\Filesystem();

// This is needed for database based session driver
$connFactory = new \Illuminate\Database\Connectors\ConnectionFactory($sessionContainer);
$sessionContainer['db'] = new \Illuminate\Database\DatabaseManager($sessionContainer, $connFactory);

// You also need to create the required database table. Example on postgres:
/*
CREATE TABLE sessions
(
id character varying(256) NOT NULL,
  user_id integer,
  last_activity integer NOT NULL,
  user_agent text,
  ip_address character varying(45),
  payload text NOT NULL,
  CONSTRAINT sessions_pk PRIMARY KEY (id)
)
WITH (
    OIDS=FALSE
);
ALTER TABLE sessions
  OWNER TO postgres;
*/

// These are the configs or you could create this in a external file
// by using a proper config directory path in the loader above
$sessionContainer['config'] = new Illuminate\Config\Repository();
$sessionContainer['config']['session.lifetime'] = 20; // Minutes idleable
$sessionContainer['config']['session.expire_on_close'] = false;
$sessionContainer['config']['session.lottery'] = array(2, 100); // lottery--how often do they sweep storage location to clear old ones?
$sessionContainer['config']['session.cookie'] = 'laravel_session';
$sessionContainer['config']['session.path'] = '/';
$sessionContainer['config']['session.domain'] = null;
// Choose the correct driver and parameters based on your configuration
$sessionContainer['config']['session.driver'] = 'database';
$sessionContainer['config']['session.table'] = 'sessions';
$sessionContainer['config']['session.connection'] = 'conn1';
$sessionContainer['config']['database.connections'] = $settings['settings']['connections'];
//$sessionContainer['config']['session.driver'] = 'file';
//$sessionContainer['config']['session.files'] = __DIR__ . '/../sessions';

$container['sessionContainer'] = $sessionContainer;
$container->register(new Ackee\LaravelSession\PimpleSessionServiceProvider);
$app->add(new Ackee\LaravelSession\Middleware($container->get('session')));

$app->get('/set', function ($request, $response, $args) {
    $this->session->set('test', 'This is my session data');
    return $response->write('Session set.');
});

$app->get('/', function ($request, $response, $args) use ($sessionContainer) {
    if ($this->session->has('test'))
        $test = $this->session->get('test');
    else
        $test = "Not set.";
    return $response->write($test);
});

$app->get('/logout', function ($request, $response, $args) {
    $this->session->clear();
    return $response->withStatus(302)->withHeader('Location', '/');
});

$app->run();
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
