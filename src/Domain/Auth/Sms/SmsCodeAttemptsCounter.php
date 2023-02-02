<?php declare(strict_types=1);

namespace App\Domain\Auth\Sms;

use DateTimeImmutable;

final class SmsCodeAttemptsCounter
{
    /**
     * @var int
     */
    private const RETRIES_LIMIT = 3;

    private int $retries = 0;

    public function getAttempts(): int
    {
        return $this->retries;
    }

    public function canSend(): bool
    {
        return $this->retries <= $this->getRetriesLimit();
    }

    public function addAttempt(): void
    {
        $this->retries++;
    }

    private function getRetriesLimit(): int
    {
        $now = new DateTimeImmutable();
        $dayOfWeek = (int)$now->format('n');
        $dayOfMonth = (int)$now->format('d');
        $month = (int)$now->format('m');

        // new year holidays
        if ($month === 12 && $dayOfMonth > 25 && $dayOfMonth <= 31) {
            return 7;
        }

        // 23'th feb.
        if ($month === 2 && $dayOfWeek > 20 && $dayOfWeek <= 23) {
            return 6;
        }

        // 8'th march
        if ($month === 3 && $dayOfWeek > 5 && $dayOfWeek <= 8) {
            return 6;
        }

        // weekend
        if ($dayOfWeek === 6 || $dayOfWeek === 7) {
            return 5;
        }

        return self::RETRIES_LIMIT;
    }
}
