<?php

use App\Domain\Order\Aggregate\Order;
use App\Domain\Order\Command\AddOrderCommand;
use App\Infrastructure\Db\DbAdapter;
use App\Infrastructure\Db\EntityManager\CartEntityManager;
use App\Infrastructure\Db\Factory\PDOFactory;
use App\Infrastructure\Db\Factory\QueryBuilderFactory;
use App\Infrastructure\Db\IdentityMap;
use App\Infrastructure\DICrawler;
use App\Infrastructure\Http\Controller\MainPageController;
use App\Infrastructure\Lib\Illuminate\Pipeline\IlluminatePipeline;
use App\Infrastructure\Lib\Illuminate\Pipeline\IlluminatePipelineBus;
use App\Infrastructure\Lib\Illuminate\Pipeline\IlluminatePipelineHub;
use App\Infrastructure\ReflectionHydrator;
use App\Infrastructure\SyncEventDispatcher;
use App\SharedKernel\HydratorInterface;
use App\SharedKernel\PipelineBusInterface;
use DI\Container;
use Doctrine\DBAL\Driver\PDO\MySQL\Driver;
use Illuminate\Contracts\Pipeline\Pipeline;
use Laminas\Diactoros\RequestFactory;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\UploadedFileFactory;
use Laminas\Diactoros\UriFactory;
use Predis\Client;
use Predis\Session\Handler;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use function DI\create;

$crawler = new DICrawler();

/** @var array<string, array> $app */
$app = require_once __DIR__ . './../config/app.php';

return [
    ...iterator_to_array($crawler->crawl()),
    RequestFactoryInterface::class => create(RequestFactory::class),
    ResponseFactoryInterface::class => create(ResponseFactory::class),
    ServerRequestFactoryInterface::class => create(ServerRequestFactory::class),
    StreamFactoryInterface::class => create(StreamFactory::class),
    UploadedFileFactoryInterface::class => create(UploadedFileFactory::class),
    UriFactoryInterface::class => create(UriFactory::class),
    MainPageController::class => create(MainPageController::class),
    DbAdapter::class => create(DbAdapter::class),
    PDO::class => function () use ($app): PDO {
        $pdoFactory = new PDOFactory(
            dbName: $app['database']['name'],
            dbUser: $app['database']['user'],
            dbPassword: $app['database']['password'],
            dbHost: $app['database']['host'],
            dbPort: $app['database']['port'],
        );

        return $pdoFactory->createPDO();
    },
    HydratorInterface::class => create(ReflectionHydrator::class),
    QueryBuilderFactory::class => function () use ($app): QueryBuilderFactory {
        return new QueryBuilderFactory(
            dbName: $app['database']['name'],
            dbUser: $app['database']['user'],
            dbPassword: $app['database']['password'],
            dbHost: $app['database']['host'],
            dbPort: $app['database']['port'],
            driver: new Driver(),
        );
    },
    PipelineBusInterface::class => function (Container $c): IlluminatePipelineBus {
        $pipeline = new IlluminatePipeline($c);
        $hub = new IlluminatePipelineHub($c, $pipeline);

        return new IlluminatePipelineBus($hub);
    },
    SessionHandlerInterface::class => function (): Handler {
        $client = new Client(
            parameters: [],
            options: [],
        );
        $options = [
            'gc_maxlifetime' => 0,
        ];

        return new Handler($client, $options);
    },
    IdentityMap::class => create(IdentityMap::class),
    'cart.entity_manager' => function (Container $c): CartEntityManager {
        return new CartEntityManager(
            $c->get(HydratorInterface::class),
            $c->get(IdentityMap::class),
            $c->get(DbAdapter::class),
        );
    },
    EventDispatcherInterface::class => create(SyncEventDispatcher::class),
    'pipeline.add_order' => function (Container $c): PipelineBusInterface {
        /** @var IlluminatePipelineBus $pipelineBus */
        $pipelineBus = $c->get(PipelineBusInterface::class);
        $callback = function (Pipeline $pipeline, AddOrderCommand $passable): Order {
            return $pipeline->send($passable)
                ->through([

                ])
                ->thenReturn();
        };

        $pipelineBus->register('add_order', $callback);

        return $pipelineBus;
    },
];
