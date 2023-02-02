<?php

namespace App\Domain\Exception;

use DomainException;
use Ramsey\Uuid\Uuid;

final class AggregateNotFoundException extends DomainException
{
    public function __construct(Uuid $id)
    {
        parent::__construct('');
    }
}
