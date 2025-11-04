<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Renderer\Cli;

use DecodeLabs\Enumerable\Backed\NamedInt;
use DecodeLabs\Enumerable\Backed\NamedIntTrait;

enum Option: int implements NamedInt
{
    use NamedIntTrait;

    case Bold = 1;
    case Dim = 2;
    case Underline = 4;
    case Blink = 5;
    case Reverse = 7;
    case Private = 8;

    public static function toReset(
        string|self $name
    ): int {
        $name = self::fromAny($name);

        return match ($name) {
            self::Bold => 22,
            self::Dim => 22,
            self::Underline => 24,
            self::Blink => 25,
            self::Reverse => 27,
            self::Private => 28
        };
    }

    public static function toValue(
        string|self $name
    ): int {
        return self::fromAny($name)->value;
    }
}
