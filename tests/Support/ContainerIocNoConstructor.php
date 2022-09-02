<?php

declare(strict_types=1);

namespace Tests\Support;

use ArrayObject;

class ContainerIocNoConstructor
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
