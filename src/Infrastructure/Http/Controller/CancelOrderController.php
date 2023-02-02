<?php

namespace App\Infrastructure\Http\Controller;

use App\Domain\Order\Command\CancelOrderCommand;
use App\Domain\Order\Handler\CancelOrderHandler;
use App\SharedKernel\Validation\ValidatorInterface;
use JsonException;
use Laminas\Diactoros\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;

final class CancelOrderController extends Controller
{
    public function __construct(
        private readonly CancelOrderHandler $handler,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @throws JsonException
     */
    public function __invoke(RequestInterface $request): ResponseInterface
    {
        $response = new Response();
        $response->withHeader('Content-Type', 'application/json');

        /** @var array $data */
        $data = json_decode(
            json: $request->getBody()->getContents(),
            associative: true,
            flags: JSON_THROW_ON_ERROR
        );
        $this->validator->validate($data, [
            'order_id' => ['required', 'string', 'uuid'],
        ]);
        $command = new CancelOrderCommand(
            orderId: Uuid::fromString($data['order_id'])
        );
        $this->handler->handle($command);

        return $response->withStatus(self::HTTP_OK);
    }
}
