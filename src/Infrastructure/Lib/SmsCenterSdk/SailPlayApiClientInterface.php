<?php declare(strict_types=1);

namespace App\Infrastructure\Lib\SmsCenterSdk;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

interface SailPlayApiClientInterface
{
    /**
     * @throws GuzzleException
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface;
}
