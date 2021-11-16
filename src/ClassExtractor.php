<?php

declare(strict_types=1);

namespace Cdn77\EntityFqnExtractor;

use Cdn77\EntityFqnExtractor\Exception\ClassDefinitionInFileIsInvalid;
use PhpParser\Error;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\ParserFactory;

use function count;
use function implode;
use function Safe\file_get_contents;

final class ClassExtractor
{
    /** @return list<class-string> */
    public static function all(string $filePathName) : array
    {
        $code = file_get_contents($filePathName);

        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);

        try {
            $ast = $parser->parse($code) ?? [];
        } catch (Error $error) {
            throw ClassDefinitionInFileIsInvalid::cannotParse($filePathName, $error);
        }

        /** @var list<class-string> $classes */
        $classes = [];

        foreach ($ast as $node) {
            if (! $node instanceof Namespace_) {
                continue;
            }

            $namespace = $node->name === null ? '' : implode('\\', $node->name->parts) . '\\';

            foreach ($node->stmts as $stmt) {
                if (! $stmt instanceof Class_) {
                    continue;
                }

                if ($stmt->name === null) {
                    continue;
                }

                /** @psalm-var class-string $class */
                $class = $namespace . $stmt->name->name;

                $classes[] = $class;
            }
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
