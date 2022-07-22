<?php

declare(strict_types=1);

namespace Cdn77\EntityFqnExtractor\Tests;

use Cdn77\EntityFqnExtractor\EnumExtractor;
use Cdn77\EntityFqnExtractor\Exception\EnumDefinitionInFileIsInvalid;
use Cdn77\EntityFqnExtractor\Fixtures\SomeDirectory\EnumFixture;
use Generator;

final class EnumExtractorTest extends TestCaseBase
{
    public function testGet(): void
    {
        self::assertSame(
            EnumFixture::class,
            EnumExtractor::get(
                __DIR__ . '/Fixtures/SomeDirectory/EnumFixture.php'
            )
        );
    }

    /**
     * @param list<class-string> $expectedClasses
     *
     * @dataProvider dataProviderAll
     */
    public function testAll(array $expectedClasses, string $fixture): void
    {
        self::assertSame(
            $expectedClasses,
            EnumExtractor::all(__DIR__ . $fixture)
        );
    }

    /** @return Generator<string, array{list<string>, string}> */
    public function dataProviderAll(): Generator
    {
        yield 'two enums' => [
            [
                'Cdn77\EntityFqnExtractor\Fixtures\SomeDirectory\Enum1',
                'Cdn77\EntityFqnExtractor\Fixtures\SomeDirectory\Enum2',
            ],
            '/Fixtures/SomeDirectory/EnumsFixture.php',
        ];

        yield 'class and interface' => [
            [
                'Cdn77\EntityFqnExtractor\Fixtures\SomeDirectory\A',
                'Cdn77\EntityFqnExtractor\Fixtures\SomeDirectory\B',
            ],
            '/Fixtures/SomeDirectory/EnumInterfaceFixture.php',
        ];
    }

    /** @dataProvider dataProviderGetThrows */
    public function testGetThrows(string $expectedMessage, string $path): void
    {
        $this->expectException(EnumDefinitionInFileIsInvalid::class);
        $this->expectExceptionMessage($expectedMessage);
        EnumExtractor::get(__DIR__ . $path);
    }

    /** @return Generator<string, array{string, string}> */
    public function dataProviderGetThrows(): Generator
    {
        yield 'class' => ['There is no enum in a file', '/Fixtures/SomeDirectory/ClassFixture.php'];
        yield 'interface' => ['There is no enum in a file', '/Fixtures/SomeDirectory/InterfaceFixture.php'];
        yield 'trait' => ['There is no enum in a file', '/Fixtures/SomeDirectory/TraitFixture.php'];
        yield 'multiple enums' => [
            'There are multiple enums in a file',
            '/Fixtures/SomeDirectory/EnumsFixture.php',
        ];
    }
}
