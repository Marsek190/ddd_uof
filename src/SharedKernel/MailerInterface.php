<?php declare(strict_types=1);

namespace App\SharedKernel;

use SplFileObject;

interface MailerInterface
{
    public function send(string $subject, string $description, ?SplFileObject $attachment, array $recipients): void;
}
