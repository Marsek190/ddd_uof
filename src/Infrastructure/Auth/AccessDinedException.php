<?php declare(strict_types=1);

namespace App\Infrastructure\Auth;

use Exception;

final class AccessDinedException extends Exception
{
    /**
     * @var int
     */
    protected $code = 401;
}
