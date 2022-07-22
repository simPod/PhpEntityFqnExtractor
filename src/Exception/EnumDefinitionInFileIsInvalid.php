<?php

declare(strict_types=1);

namespace Cdn77\EntityFqnExtractor\Exception;

use InvalidArgumentException;
use PhpParser\Error;

use function Safe\sprintf;

final class EnumDefinitionInFileIsInvalid extends InvalidArgumentException
{
    public static function cannotParse(string $filePathName, Error $error): self
    {
        return new self(
            sprintf('Cannot parse file %s', $filePathName),
            previous: $error
        );
    }

    public static function noEnum(string $filePathName): self
    {
        return new self(
            sprintf('There is no enum in a file %s', $filePathName)
        );
    }

    public static function multipleEnums(string $filePathName): self
    {
        return new self(
            sprintf('There are multiple enums in a file %s', $filePathName)
        );
    }
}
