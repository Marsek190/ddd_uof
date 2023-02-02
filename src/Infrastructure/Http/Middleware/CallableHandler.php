<?php

namespace App\Infrastructure\Http\Middleware;

use Exception;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use UnexpectedValueException;

final class CallableHandler implements MiddlewareInterface, RequestHandlerInterface
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * @param callable $callable
     * @param ResponseFactoryInterface $responseFactory
     */
    public function __construct(callable $callable, private readonly ResponseFactoryInterface $responseFactory)
    {
        $this->callable = $callable;
    }

    /**
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->execute([$request]);
    }

    /**
     * @throws Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->execute([$request, $handler]);
    }

    /**
     * @throws Exception
     */
    public function __invoke(): ResponseInterface
    {
        return $this->execute(func_get_args());
    }

    /**
     * @throws Exception
     */
    private function execute(array $arguments = []): ResponseInterface
    {
        ob_start();
        $level = ob_get_level();

        try {
            $return = call_user_func_array($this->callable, $arguments);

            if ($return instanceof ResponseInterface) {
                $response = $return;
                $return = '';
            } elseif (is_null($return)
                || is_scalar($return)
                || (is_object($return) && method_exists($return, '__toString'))
            ) {
                $response = $this->responseFactory->createResponse();
            } else {
                throw new UnexpectedValueException(
                    'The value returned must be scalar or an object with __toString method'
                );
            }

            while (ob_get_level() >= $level) {
                $return = ob_get_clean() . $return;
            }

            $return = (string)$return;
            $body = $response->getBody();

            if ($return !== '' && $body->isWritable()) {
                $body->write($return);
            }

            return $response;
        } catch (Exception $exception) {
            while (ob_get_level() >= $level) {
                ob_end_clean();
            }

            throw $exception;
        }
    }
}