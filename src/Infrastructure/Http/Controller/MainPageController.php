<?php

namespace App\Infrastructure\Http\Controller;

use Laminas\Diactoros\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class MainPageController extends Controller
{
    public function __invoke(RequestInterface $request): ResponseInterface
    {
        $response = new Response();
        $response->withHeader('Content-Type', 'text/html');
        $response->getBody()->write('main page');

        return $response->withStatus(self::HTTP_OK);
    }
}
