<?php declare(strict_types=1);

namespace App\Domain;

use App\SharedKernel\ConvertableToArrayTrait;
use Ramsey\Uuid\UuidInterface;

abstract class AggregateRoot
{
    use ConvertableToArrayTrait;

    /**
     * @var array<string, Event>
     */
    private array $events = [];

    /**
     * @return Event[]
     */
    public final function popEvents(): array
    {
        $events = array_values($this->events);
        $this->events = [];

        return $events;
    }

    protected final function raiseEvent(Event $event): void
    {
        if (isset($this->events[$event::class])) {
            return;
        }

        $this->events[$event::class] = $event;
    }

    abstract public function getId(): UuidInterface;
}
