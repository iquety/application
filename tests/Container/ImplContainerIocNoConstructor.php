<?php

declare(strict_types=1);

namespace Tests\Container;

use ArrayObject;

/** @codeCoverageIgnore */
class ImplContainerIocNoConstructor
{
    /**
     * @param ArrayObject<int,string> $object
     * @return array<int,string>
    */
    public function injectedMethod(ArrayObject $object): array
    {
        return $object->getArrayCopy();
    }
}
