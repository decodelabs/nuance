<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance;

use DecodeLabs\Nuance\Entity\NativeObject;

interface Dumpable
{
    public function nuanceDump(): NativeObject;
}
