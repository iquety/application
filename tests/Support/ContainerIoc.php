<?php

declare(strict_types=1);

namespace Tests\Support;

use ArrayObject;
use stdClass;

/** @SuppressWarnings(PHPMD.ShortVariable) */
class ContainerIoc
{
    /** @var array<int,string> */
    private array $values;

    // Construtor só aceita injeções de objetos
    /**
     * @param ArrayObject<int,string> $object
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(ArrayObject $object, stdClass $class = null)
    {
        $this->values = func_get_args();
    }

    /** @return array<int,string> */
    public function values(): array
    {
        return $this->values;
    }

     /**
     * @param ArrayObject<int,string> $object
     * @return array<int,mixed>
    */
    public function injectedMethod(ArrayObject $object): array
    {
        return $object->getArrayCopy();
    }

     /**
     * @param ArrayObject<int,string> $object
     * @return array<int,mixed>
    */
    public function injectedMethodExtraArguments(ArrayObject $object, int $id, string $name): array
    {
        return [ current($object->getArrayCopy()), $id, $name ];
    }

     /**
     * @param ArrayObject<int,string> $object
     * @return array<int,mixed>
    */
    public function injectedMethodExtraDefaultValueArguments(
        ArrayObject $object,
        int $id = 33,
        string $name = "Ricardo"
    ): array {
        return [ current($object->getArrayCopy()), $id, $name ];
    }

    /**
     * @param ArrayObject<int,string> $object
     * @return array<int,string>
    */
    public static function injectedStaticMethod(ArrayObject $object): array
    {
        return $object->getArrayCopy();
    }

    /**
     * @param ArrayObject<int,string> $object
     * @return array<int,string>
    */
    public function __invoke(ArrayObject $object): array
    {
        return $object->getArrayCopy();
    }
}
