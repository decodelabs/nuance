<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity;

use DecodeLabs\Nuance\Entity;

interface Value extends Entity
{
    public mixed $value { get; }
}
