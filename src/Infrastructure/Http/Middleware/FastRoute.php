<?php

namespace App\Infrastructure\Http\Middleware;

use FastRoute\Dispatcher;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

final class FastRoute implements MiddlewareInterface
{
    public const REQUEST_HANDLER_ATTRIBUTE = 'request-handler';

    private ResponseFactoryInterface $responseFactory;

    /**
     * @param Dispatcher $router
     * @param ResponseFactoryInterface $responseFactory
     */
    public function __construct(private readonly Dispatcher $router, ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $this->router->dispatch($request->getMethod(), rawurldecode($request->getUri()->getPath()));

        if ($route[0] === Dispatcher::NOT_FOUND) {
            return $this->responseFactory->createResponse(404);
        }

        if ($route[0] === Dispatcher::METHOD_NOT_ALLOWED) {
            return $this->responseFactory->createResponse(405)
                ->withHeader('Allow', implode(', ', $route[1]));
        }

        foreach ($route[2] as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }

        $request = $this->setHandler($request, $route[1]);

        return $handler->handle($request);
    }

    protected function setHandler(ServerRequestInterface $request, mixed $handler): ServerRequestInterface
    {
        return $request->withAttribute(self::REQUEST_HANDLER_ATTRIBUTE, $handler);
    }
}
