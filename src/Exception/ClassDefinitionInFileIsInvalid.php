<?php

declare(strict_types=1);

namespace Cdn77\EntityFqnExtractor\Exception;

use InvalidArgumentException;
use SplFileInfo;

use function Safe\sprintf;

final class ClassDefinitionInFileIsInvalid extends InvalidArgumentException
{
    public static function noClass(SplFileInfo $fileInfo) : self
    {
        return new self(
            sprintf('There is no class in a file %s', $fileInfo->getPathname())
        );
    }

    public static function multipleClasses(SplFileInfo $fileInfo) : self
    {
        return new self(
            sprintf('There are multiple classes in a file %s', $fileInfo->getPathname())
        );
    }
}
