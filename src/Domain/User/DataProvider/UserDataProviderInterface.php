<?php declare(strict_types=1);

namespace App\Domain\User\DataProvider;

use App\Domain\User\Aggregate\User;
use App\Domain\User\ValueObject\Phone;
use Ramsey\Uuid\UuidInterface;

interface UserDataProviderInterface
{
    public function get(UuidInterface $id): ?User;
    public function getByPhone(Phone $phone): ?User;
}
