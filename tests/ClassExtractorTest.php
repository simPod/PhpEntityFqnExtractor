<?php

declare(strict_types=1);

namespace Cdn77\EntityFqnExtractor\Tests;

use Cdn77\EntityFqnExtractor\ClassExtractor;
use Cdn77\EntityFqnExtractor\Exception\ClassDefinitionInFileIsInvalid;
use Cdn77\EntityFqnExtractor\Fixtures\SomeDirectory\ClassFixture;
use Generator;
use SplFileInfo;

final class ClassExtractorTest extends TestCaseBase
{
    public function testGet() : void
    {
        self::assertSame(
            ClassFixture::class,
            ClassExtractor::get(
                new SplFileInfo(__DIR__ . '/Fixtures/SomeDirectory/ClassFixture.php')
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
            ClassExtractor::all(new SplFileInfo(__DIR__ . $fixture))
        );
    }

    /** @return Generator<string, list<list<string>, string>> */
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
    public function testGetThrows(string $path) : void
    {
        $this->expectException(ClassDefinitionInFileIsInvalid::class);
        ClassExtractor::get(new SplFileInfo(__DIR__ . $path));
    }

    /** @return Generator<string, list<string>> */
    public function dataProviderGetThrows() : Generator
    {
        yield 'interface' => ['/Fixtures/SomeDirectory/InterfaceFixture.php'];
        yield 'trait' => ['/Fixtures/SomeDirectory/TraitFixture.php'];
        yield 'multiple classes' => ['/Fixtures/SomeDirectory/ClassesFixture.php'];
    }
}
