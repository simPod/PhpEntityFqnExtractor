<?php

declare(strict_types=1);

namespace Cdn77\EntityFqnExtractor\Exception;

use InvalidArgumentException;

use function Safe\sprintf;

final class ClassDefinitionInFileIsInvalid extends InvalidArgumentException
{
    public static function noClass(string $filePathName) : self
    {
        return new self(
            sprintf('There is no class in a file %s', $filePathName)
        );
    }

    public static function multipleClasses(string $filePathName) : self
    {
        return new self(
            sprintf('There are multiple classes in a file %s', $filePathName)
        );
    }
}
