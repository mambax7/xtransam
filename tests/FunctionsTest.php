<?php

namespace Xtransam\Tests;

use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    /**
     * @return array<string, array{0:string}>
     */
    public function storeMethodProvider(): array
    {
        return [
            'urlcode' => ['urlcode'],
            'base64'  => ['base64'],
            'uucode'  => ['uucode'],
            'open'    => ['open'],
            'hex'     => ['hex'],
        ];
    }

    /**
     * @dataProvider storeMethodProvider
     */
    public function testConvertEncodeDecodeRoundTrip(string $method): void
    {
        $value = 'Tést value with symbols % and unicode ✓';

        $encoded = xtransam_convert_encode($value, $method);
        $decoded = xtransam_convert_decode($encoded, $method);

        if ('hex' === $method) {
            self::assertNotNull($decoded);
        }

        $this->assertSame($value, $decoded);
    }

    public function testHex2BinRejectsOddLength(): void
    {
        $this->assertNull(xtransam_hex2bin('abc'));
    }

    public function testHex2BinConvertsEvenLengthStrings(): void
    {
        $this->assertSame('AB', xtransam_hex2bin('4142'));
    }
}
