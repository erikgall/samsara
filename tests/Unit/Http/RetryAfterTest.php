<?php

namespace Samsara\Tests\Unit\Http;

use Samsara\Tests\TestCase;
use Samsara\Http\RetryAfter;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @author Erik Galloway <erik@erikgall.com>
 */
class RetryAfterTest extends TestCase
{
    #[Test]
    #[DataProvider('retryAfterValues')]
    public function it_parses_retry_after_against_the_laravel_clock(?string $header, ?int $expected): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-21 12:00:00 UTC'));

        try {
            $this->assertSame($expected, RetryAfter::seconds($header));
        } finally {
            Carbon::setTestNow();
        }
    }

    /**
     * @return array<string, array{?string, ?int}>
     */
    public static function retryAfterValues(): array
    {
        return [
            'numeric'               => ['2', 2],
            'whitespace'            => [' 2 ', 2],
            'zero'                  => ['0', 0],
            'future date'           => ['Wed, 21 Oct 2026 13:00:00 GMT', 3600],
            'expired date'          => ['Wed, 21 Oct 2026 11:00:00 GMT', 0],
            'missing'               => [null, null],
            'invalid'               => ['invalid', null],
            'empty'                 => ['', null],
            'negative seconds'      => ['-5', null],
            'negative offset'       => ['-1', null],
            'relative day'          => ['tomorrow', null],
            'relative hour'         => ['+1 hour', null],
            'relative seconds'      => ['5 seconds', null],
            'current time'          => ['now', null],
            'fractional seconds'    => ['1.5', null],
            'invalid calendar date' => ['Tue, 31 Feb 2026 13:00:00 GMT', null],
            'incorrect weekday'     => ['Thu, 21 Oct 2026 13:00:00 GMT', null],
            'non-GMT timezone'      => ['Wed, 21 Oct 2026 13:00:00 UTC', null],
            'null byte'             => ["Wed, 21 Oct 2026 13:00:00 GMT\0", null],
        ];
    }
}
