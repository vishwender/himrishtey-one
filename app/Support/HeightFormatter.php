<?php

namespace App\Support;

final class HeightFormatter
{
    public static function format(mixed $height, string $emptyValue = '-'): string
    {
        if ($height === null) {
            return $emptyValue;
        }

        $value = trim((string) $height);

        if ($value === '') {
            return $emptyValue;
        }

        if (preg_match('/^(\d+)\s*(?:ft|feet|\')\s*(\d+)\s*(?:in|inches|\")?$/i', $value, $matches)) {
            return self::fromParts((int) $matches[1], (int) $matches[2]);
        }

        if (! preg_match('/^(\d+)(?:\.(\d+))?$/', $value, $matches)) {
            return $value;
        }

        return self::fromParts(
            (int) $matches[1],
            isset($matches[2]) ? (int) $matches[2] : 0,
        );
    }

    private static function fromParts(int $feet, int $inches): string
    {
        return sprintf('%d ft %d in', $feet, $inches);
    }
}
