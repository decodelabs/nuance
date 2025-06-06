<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity;

use DecodeLabs\Nuance\Entity;
use DecodeLabs\Remnant\Trace;

interface Traceable extends Entity
{
    public ?Trace $stackTrace { get; }
}
