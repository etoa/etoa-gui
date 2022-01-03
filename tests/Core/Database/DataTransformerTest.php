<?php declare(strict_types=1);

namespace EtoA\Core\Database;

use PHPUnit\Framework\TestCase;

class DataTransformerTest extends TestCase
{
    /**
     * @dataProvider userStringProvider
     */
    public function testUserString(string $string, array $expected): void
    {
        $this->assertSame($expected, DataTransformer::userString($string));
    }

    public function userStringProvider(): array
    {
        return [
            ['', []],
            [',1,2,', [1, 2]],
        ];
    }

    /**
     * @dataProvider dataStringProvider
     */
    public function testDataString(string $string, array $expected): void
    {
        $this->assertSame($expected, DataTransformer::dataString($string));
    }

    public function dataStringProvider(): array
    {
        return [
            ['', []],
            [',1:12,2:2,', [1 => 12, 2 => 2]],
        ];
    }
}
