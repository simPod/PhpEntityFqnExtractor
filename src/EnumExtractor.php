<?php

declare(strict_types=1);

namespace Cdn77\EntityFqnExtractor;

use Cdn77\EntityFqnExtractor\Exception\EnumDefinitionInFileIsInvalid;

use function count;
use function ltrim;
use function Safe\file_get_contents;
use function token_get_all;

use const T_ENUM;
use const T_NAME_QUALIFIED;
use const T_NAMESPACE;
use const T_STRING;
use const T_WHITESPACE;

final class EnumExtractor
{
    /** @return list<class-string> */
    public static function all(string $filePathName): array
    {
        $code = file_get_contents($filePathName);

        /** @var list<class-string> $enums */
        $enums = [];
        $namespace = '';
        $tokens = token_get_all($code);
        $count = count($tokens);

        foreach ($tokens as $i => $token) {
            if ($i < 2) {
                continue;
            }

            if ($token[0] === T_NAMESPACE) {
                for ($j = $i + 1; $j < $count; ++$j) {
                    if ($tokens[$j][0] === T_NAME_QUALIFIED) {
                        $namespace = $tokens[$j][1];

                        break;
                    }

                    if ($tokens[$j][0] === T_STRING) {
                        $namespace .= '\\' . $tokens[$j][1];

                        continue;
                    }

                    if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                        $namespace = ltrim($namespace, '\\');

                        break;
                    }
                }

                continue;
            }

            if (
                $tokens[$i - 2][0] !== T_ENUM
                || $tokens[$i - 1][0] !== T_WHITESPACE
                || $token[0] !== T_STRING
            ) {
                continue;
            }

            $enumName = $tokens[$i][1];
            /** @psalm-var class-string $fqn */
            $fqn = $namespace . '\\' . $enumName;
            $enums[] = $fqn;
        }

        if ($enums === []) {
            throw EnumDefinitionInFileIsInvalid::noEnum($filePathName);
        }

        return $enums;
    }

    /** @return class-string */
    public static function get(string $filePathName): string
    {
        $enums = self::all($filePathName);

        if (count($enums) > 1) {
            throw EnumDefinitionInFileIsInvalid::multipleEnums($filePathName);
        }

        return $enums[0];
    }
}
