<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */

use App\Infrastructure\Http\Middleware\ExceptionHandler;
use App\Infrastructure\Http\Middleware\FastRoute;
use App\Infrastructure\Http\Middleware\RequestHandler;
use DI\ContainerBuilder;
use Narrowspark\HttpEmitter\SapiEmitter;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Relay\Relay;
use function FastRoute\simpleDispatcher;

require_once __DIR__ . './../vendor/autoload.php';

$builder = new ContainerBuilder();
$builder->useAutowiring(true);
$builder->useAttributes(false);
$builder->enableDefinitionCache(__DIR__ . './../storage/var/cache');

/** @var array $dependencyDefinitions */
$dependencyDefinitions = require_once __DIR__ . './di.php';

$builder->addDefinitions($dependencyDefinitions);

$container = $builder->build();

/** @var callable $routeDefinitionCallback */
$routeDefinitionCallback = include_once __DIR__ . './routes.php';

$router = simpleDispatcher($routeDefinitionCallback);

/** @var ResponseFactoryInterface $responseFactory */
$responseFactory = $container->get(ResponseFactoryInterface::class);

/** @var StreamFactoryInterface $streamFactory */
$streamFactory = $container->get(StreamFactoryInterface::class);

$middlewares = [
    new FastRoute(
        $router,
        $responseFactory
    ),
    new RequestHandler($container),
    new ExceptionHandler($responseFactory, $streamFactory),
];

$requestHandler = new Relay($middlewares);
$response = $requestHandler->handle(ServerRequestFactory::fromGlobals());

$emitter = new SapiEmitter();

return $emitter->emit($response);
