<?php

declare(strict_types=1);

namespace Cdn77\EntityFqnExtractor;

use Cdn77\EntityFqnExtractor\Exception\ClassDefinitionInFileIsInvalid;

use function count;
use function ltrim;
use function Safe\file_get_contents;
use function token_get_all;

use const T_CLASS;
use const T_NAMESPACE;
use const T_STRING;
use const T_WHITESPACE;

final class ClassExtractor
{
    /** @return list<class-string> */
    public static function all(string $filePathName) : array
    {
        $contents = file_get_contents($filePathName);
        /** @var list<class-string> $classes */
        $classes = [];
        $namespace = '';
        $tokens = token_get_all($contents);
        $count = count($tokens);

        foreach ($tokens as $i => $token) {
            if ($i < 2) {
                continue;
            }

            if ($token[0] === T_NAMESPACE) {
                for ($j = $i + 1; $j < $count; ++$j) {
                    if ($tokens[$j][0] === 314) { // T_NAME_QUALIFIED PHP 8
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
                $tokens[$i - 2][0] !== T_CLASS
                || $tokens[$i - 1][0] !== T_WHITESPACE
                || $token[0] !== T_STRING
            ) {
                continue;
            }

            $className = $tokens[$i][1];
            /** @psalm-var class-string $fqn */
            $fqn = $namespace . '\\' . $className;
            $classes[] = $fqn;
        }

        if ($classes === []) {
            throw ClassDefinitionInFileIsInvalid::noClass($filePathName);
        }

        return $classes;
    }

    /** @return class-string */
    public static function get(string $filePathName) : string
    {
        $classes = self::all($filePathName);

        if (count($classes) > 1) {
            throw ClassDefinitionInFileIsInvalid::multipleClasses($filePathName);
        }

        return $classes[0];
    }
}
