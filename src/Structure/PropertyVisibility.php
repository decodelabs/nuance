<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Structure;

enum PropertyVisibility: string
{
    case Public = 'public';
    case Protected = 'protected';
    case Private = 'private';
}
