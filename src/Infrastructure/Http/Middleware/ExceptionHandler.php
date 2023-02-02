<?php

namespace App\Infrastructure\Http\Middleware;

use App\Infrastructure\Http\Controller\Controller;
use App\SharedKernel\Validation\ValidationException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

final class ExceptionHandler implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly StreamFactoryInterface $streamFactory,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (ValidationException $e) {
            $response = $this->responseFactory->createResponse(Controller::HTTP_BAD_REQUEST);
            $body = $this->streamFactory->createStream(json_encode(['error' => $e->getMessage()]));

            return $response->withBody($body)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $e) {
            dd($e->getMessage());
        }
    }
}
