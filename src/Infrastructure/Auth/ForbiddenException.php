<?php declare(strict_types=1);

namespace App\Infrastructure\Auth;

use Exception;

final class ForbiddenException extends Exception
{
    /**
     * @var int
     */
    protected $code = 403;
}
