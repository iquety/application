<?php

declare(strict_types=1);

// @codeCoverageIgnoreStart
/**
 * @param ArrayObject<int,string> $object
 * @return array<int,string>
*/
function declaredFunction(ArrayObject $object): array
{
    return $object->getArrayCopy();
}
// @codeCoverageIgnoreEnd
