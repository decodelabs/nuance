<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Renderer\Cli;

use DecodeLabs\Enumerable\Backed\NamedInt;
use DecodeLabs\Enumerable\Backed\NamedIntTrait;

enum Foreground: int implements NamedInt
{
    use NamedIntTrait;

    case Black = 30;
    case Red = 31;
    case Green = 32;
    case Yellow = 33;
    case Blue = 34;
    case Magenta = 35;
    case Cyan = 36;
    case White = 37;
    case Reset = 39;

    public static function toValue(
        string|self $name
    ): int {
        return self::fromAny($name)->value;
    }
}
