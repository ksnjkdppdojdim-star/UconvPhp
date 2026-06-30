<?php

namespace Uconv;

/**
 * Simple in-process text counter.
 */
class Counter
{
    /**
     * @var array<string, int>
     */
    private static array $counts = [];

    /**
     * Increment a text counter and return the new value.
     */
    public static function increment(string $text, int $step = 1): int
    {
        self::assertText($text);

        if ($step < 1) {
            throw new \InvalidArgumentException('Counter step must be greater than 0.');
        }

        self::$counts[$text] = self::get($text) + $step;

        return self::$counts[$text];
    }

    /**
     * Return the current value for a text counter.
     */
    public static function get(string $text): int
    {
        self::assertText($text);

        return self::$counts[$text] ?? 0;
    }

    /**
     * Reset a text counter and return the new value.
     */
    public static function reset(string $text): int
    {
        self::assertText($text);

        unset(self::$counts[$text]);

        return 0;
    }

    /**
     * Reset every text counter.
     */
    public static function resetAll(): int
    {
        self::$counts = [];

        return 0;
    }

    /**
     * Return every counted text and its current value.
     *
     * @return array<string, int>
     */
    public static function all(): array
    {
        return self::$counts;
    }

    private static function assertText(string $text): void
    {
        if ($text === '') {
            throw new \InvalidArgumentException('Counter text must not be empty.');
        }
    }
}
