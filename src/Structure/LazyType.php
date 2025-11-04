<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Structure;

enum LazyType: string
{
    case Ghost = 'ghost';
    case Proxy = 'proxy';
    case Unknown = 'lazy';
}
