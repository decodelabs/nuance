<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance;

use DecodeLabs\Nuance\Entity\NativeObject;

interface Dumpable
{
    public function toNuanceEntity(): NativeObject;
}
