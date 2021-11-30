<?php

declare(strict_types=1);

namespace Cdn77\EntityFqnExtractor\Tests;

use Cdn77\EntityFqnExtractor\ClassExtractor;
use Cdn77\EntityFqnExtractor\Exception\ClassDefinitionInFileIsInvalid;
use Cdn77\EntityFqnExtractor\Fixtures\SomeDirectory\ClassFixture;
use Generator;

use const PHP_VERSION_ID;

final class ClassExtractorTest extends TestCaseBase
{
    public function testGet() : void
    {
        self::assertSame(
            ClassFixture::class,
            ClassExtractor::get(
                __DIR__ . '/Fixtures/SomeDirectory/ClassFixture.php'
            )
        );
    }

    /**
     * @param list<class-string> $expectedClasses
     *
     * @dataProvider dataProviderAll
     */
    public function testAll(array $expectedClasses, string $fixture) : void
    {
        self::assertSame(
            $expectedClasses,
            ClassExtractor::all(__DIR__ . $fixture)
        );
    }

    /** @return Generator<string, array{list<string>, string}> */
    public function dataProviderAll() : Generator
    {
        yield 'two classes' => [
            [
                'Cdn77\EntityFqnExtractor\Fixtures\SomeDirectory\A',
                'Cdn77\EntityFqnExtractor\Fixtures\SomeDirectory\B',
            ],
            '/Fixtures/SomeDirectory/ClassesFixture.php',
        ];

        yield 'class and interface' => [
            [
                'Cdn77\EntityFqnExtractor\Fixtures\SomeDirectory\A',
                'Cdn77\EntityFqnExtractor\Fixtures\SomeDirectory\B',
            ],
            '/Fixtures/SomeDirectory/ClassInterfaceFixture.php',
        ];
    }

    /** @dataProvider dataProviderGetThrows */
    public function testGetThrows(string $expectedMessage, string $path) : void
    {
        $this->expectException(ClassDefinitionInFileIsInvalid::class);
        $this->expectExceptionMessage($expectedMessage);
        ClassExtractor::get(__DIR__ . $path);
    }

    /** @return Generator<string, array{string, string}> */
    public function dataProviderGetThrows() : Generator
    {
        if (PHP_VERSION_ID >= 80100) {
            yield 'enum' => ['There is no class in a file', '/Fixtures/SomeDirectory/EnumFixture.php'];
        }

        yield 'interface' => ['There is no class in a file', '/Fixtures/SomeDirectory/InterfaceFixture.php'];
        yield 'trait' => ['There is no class in a file', '/Fixtures/SomeDirectory/TraitFixture.php'];
        yield 'multiple classes' => [
            'There are multiple classes in a file',
            '/Fixtures/SomeDirectory/ClassesFixture.php',
        ];
    }
}
