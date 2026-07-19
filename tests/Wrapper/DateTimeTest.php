<?php

declare(strict_types=1);

namespace Wrapper;

use DateTime as PhpDateTime;
use DateTimeImmutable;
use DateTimeZone;
use jeroendn\PhpHelpers\Wrapper\DateTime;
use PHPUnit\Framework\TestCase;

final class DateTimeTest extends TestCase
{
    public function testConstructWithoutArgumentCreatesMutableNow(): void
    {
        $before = new PhpDateTime();
        $wrapper = new DateTime();
        $after = new PhpDateTime();

        self::assertFalse($wrapper->isFrozen());
        self::assertInstanceOf(PhpDateTime::class, $wrapper->getDateTime());
        self::assertGreaterThanOrEqual($before, $wrapper->getDateTime());
        self::assertLessThanOrEqual($after, $wrapper->getDateTime());
    }

    public function testConstructWithMutableDateTimeKeepsValueAndStaysUnfrozen(): void
    {
        $source = new PhpDateTime('2024-02-29 13:37:42.123456');
        $wrapper = new DateTime($source);

        self::assertFalse($wrapper->isFrozen());
        self::assertInstanceOf(PhpDateTime::class, $wrapper->getDateTime());
        self::assertSame('2024-02-29 13:37:42.123456', $wrapper->getDateTime()->format('Y-m-d H:i:s.u'));
    }

    public function testConstructWithImmutableDateTimeKeepsValueAndIsFrozen(): void
    {
        $source = new DateTimeImmutable('2024-02-29 13:37:42.123456');
        $wrapper = new DateTime($source);

        self::assertTrue($wrapper->isFrozen());
        self::assertInstanceOf(DateTimeImmutable::class, $wrapper->getDateTime());
        self::assertSame('2024-02-29 13:37:42.123456', $wrapper->getDateTime()->format('Y-m-d H:i:s.u'));
    }

    public function testConstructCopiesMutableDateTime(): void
    {
        $source = new PhpDateTime('2024-02-29 13:37:42');
        $wrapper = new DateTime($source);

        $source->modify('+1 day');

        self::assertSame('2024-02-29 13:37:42', $wrapper->getDateTime()->format('Y-m-d H:i:s'));
    }

    public function testConstructPreservesTimezone(): void
    {
        $source = new PhpDateTime('2024-02-29 13:37:42', new DateTimeZone('America/New_York'));
        $wrapper = new DateTime($source);

        self::assertSame('America/New_York', $wrapper->getDateTime()->getTimezone()->getName());
    }

    public function testFreezeConvertsToImmutableAndKeepsValue(): void
    {
        $wrapper = new DateTime(new PhpDateTime('2024-02-29 13:37:42.123456'));

        $wrapper->freeze();

        self::assertTrue($wrapper->isFrozen());
        self::assertInstanceOf(DateTimeImmutable::class, $wrapper->getDateTime());
        self::assertSame('2024-02-29 13:37:42.123456', $wrapper->getDateTime()->format('Y-m-d H:i:s.u'));
    }

    public function testFreezeWhenAlreadyFrozenKeepsSameInstance(): void
    {
        $wrapper = new DateTime(new DateTimeImmutable('2024-02-29 13:37:42'));
        $frozen = $wrapper->getDateTime();

        $wrapper->freeze();

        self::assertSame($frozen, $wrapper->getDateTime());
    }

    public function testUnfreezeConvertsToMutableAndKeepsValue(): void
    {
        $wrapper = new DateTime(new DateTimeImmutable('2024-02-29 13:37:42.123456'));

        $wrapper->unfreeze();

        self::assertFalse($wrapper->isFrozen());
        self::assertInstanceOf(PhpDateTime::class, $wrapper->getDateTime());
        self::assertSame('2024-02-29 13:37:42.123456', $wrapper->getDateTime()->format('Y-m-d H:i:s.u'));
    }

    public function testUnfreezeWhenAlreadyMutableKeepsSameInstance(): void
    {
        $wrapper = new DateTime(new PhpDateTime('2024-02-29 13:37:42'));
        $mutable = $wrapper->getDateTime();

        $wrapper->unfreeze();

        self::assertSame($mutable, $wrapper->getDateTime());
    }

    public function testFreezeUnfreezeRoundTripPreservesValueAndTimezone(): void
    {
        $wrapper = new DateTime(new PhpDateTime('2024-02-29 13:37:42.123456', new DateTimeZone('America/New_York')));

        $wrapper->freeze();
        $wrapper->unfreeze();

        self::assertFalse($wrapper->isFrozen());
        self::assertSame('2024-02-29 13:37:42.123456', $wrapper->getDateTime()->format('Y-m-d H:i:s.u'));
        self::assertSame('America/New_York', $wrapper->getDateTime()->getTimezone()->getName());
    }
}
