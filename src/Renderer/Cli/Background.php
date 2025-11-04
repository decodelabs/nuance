<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Renderer\Cli;

use DecodeLabs\Enumerable\Backed\NamedInt;
use DecodeLabs\Enumerable\Backed\NamedIntTrait;

enum Background: int implements NamedInt
{
    use NamedIntTrait;

    case Black = 40;
    case Red = 41;
    case Green = 42;
    case Yellow = 43;
    case Blue = 44;
    case Magenta = 45;
    case Cyan = 46;
    case White = 47;
    case Reset = 49;

    public static function toValue(
        string|self $name
    ): int {
        return self::fromAny($name)->value;
    }
}
