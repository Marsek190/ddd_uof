<?php

namespace App\Domain\Auth\Aggregate;

use App\Domain\AggregateRoot;
use App\Domain\Auth\Exception\VerificationCodeIsWrongException;
use App\Domain\Auth\ValueObject\VerificationCodeInterface;
use App\Domain\User\ValueObject\Phone;
use DateTimeImmutable;
use DomainException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class SmsCode extends AggregateRoot
{
    /**
     * @var int
     */
    public const PHONE_LOCKING_TIMEOUT = 60;

    /**
     * @var int
     */
    private const EXPIRATION_MINUTES = 5;

    /**
     * @var int
     */
    private const MAX_RETIRES = 3;

    private UuidInterface $id;

    private VerificationCodeInterface $code;

    private ?DateTimeImmutable $codeSentAt = null;

    private DateTimeImmutable $createdAt;

    private int $retries = 0;

    /**
     * @param Phone $phone
     */
    public function __construct(private readonly Phone $phone)
    {
        $this->id = Uuid::uuid4();
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getCode(): VerificationCodeInterface
    {
        return $this->code;
    }

    public function getPhone(): Phone
    {
        return $this->phone;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getRetries(): int
    {
        return $this->retries;
    }

    /**
     * @throws DomainException
     */
    public function send(VerificationCodeInterface $code): void
    {
        if ($this->isPhoneTemporaryLocked()) {
            throw new DomainException('Превышено количество попыток.');
        }

        $this->code = $code;
        $this->codeSentAt = new DateTimeImmutable();
    }

    public function getRetriesLeft(): int
    {
        return self::MAX_RETIRES - $this->retries;
    }

    public function getCodeSentAt(): ?DateTimeImmutable
    {
        return $this->codeSentAt;
    }

    public function verify(VerificationCodeInterface $code): void
    {
        if (!$this->canAddRetry()) {
            throw new DomainException('Вы превысили лимит подтверждений. Запросите новый код.');
        }

        if ($this->isCodeExpired()) {
            throw new DomainException('Код больше не действителен. Запросите новый.');
        }

        $this->retries++;

        if (!$this->code->equals($code)) {
            throw new VerificationCodeIsWrongException($this->getRetriesLeft());
        }
    }

    public function isValid(): bool
    {
        return $this->canAddRetry() && !$this->isCodeExpired();
    }

    private function canAddRetry(): bool
    {
        return $this->retries < self::MAX_RETIRES;
    }

    private function isCodeExpired(): bool
    {
        if (null === $this->codeSentAt) {
            return false;
        }

        $codeExpiresAt = $this->codeSentAt->modify(sprintf('+%d minutes', self::EXPIRATION_MINUTES));

        return (new DateTimeImmutable()) > $codeExpiresAt;
    }

    private function isPhoneTemporaryLocked(): bool
    {
        if (null === $this->codeSentAt) {
            return false;
        }

        $canRetryAt = $this->codeSentAt->modify(sprintf('+%d seconds', self::PHONE_LOCKING_TIMEOUT));

        return (new DateTimeImmutable()) < $canRetryAt;
    }
}
