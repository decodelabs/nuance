<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity;

use DecodeLabs\Nuance\Entity;
use DecodeLabs\Remnant\Trace;

interface Traceable extends Entity
{
    public ?Trace $stackTrace { get; }
}
