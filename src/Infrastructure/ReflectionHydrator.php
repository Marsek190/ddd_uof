<?php declare(strict_types=1);

namespace App\Infrastructure;

use App\SharedKernel\HydratorInterface;
use ReflectionClass;
use ReflectionException;
use InvalidArgumentException;

final class ReflectionHydrator implements HydratorInterface
{
    /**
     * @var array<string, ReflectionClass>
     */
    private array $reflectionClasses = [];

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function hydrate(string $className, array $data): object
    {
        $reflection = $this->getReflectionClass($className);
        $object = $reflection->newInstanceWithoutConstructor();

        foreach ($data as $propertyName => $propertyValue) {
            if (!$reflection->hasProperty($propertyName)) {
                throw new InvalidArgumentException(
                    sprintf('No property exists "%s" in class "%s".', $propertyName, $className)
                );
            }

            $property = $reflection->getProperty($propertyName);

            if ($property->isPrivate() || $property->isProtected()) {
                /** @noinspection PhpExpressionResultUnusedInspection */
                $property->setAccessible(true);
            }

            $property->setValue($object, $propertyValue);
        }

        return $object;
    }

    /**
     * @throws ReflectionException
     */
    protected function getReflectionClass(string $className): ReflectionClass
    {
        if (!isset($this->reflectionClasses[$className])) {
            $this->reflectionClasses[$className] = new ReflectionClass($className);
        }

        return $this->reflectionClasses[$className];
    }
}
