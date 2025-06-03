<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject\Ds;

use DecodeLabs\Nuance\Entity\NativeObject;
use Ds\Set as DsSet;

class Set extends NativeObject
{
    /**
     * @param DsSet<mixed> $set
     */
    public function __construct(
        DsSet $set,
    ) {
        parent::__construct($set);

        $this->length = count($set);
        $this->meta['capacity'] = $set->capacity();
        $this->values = $set->toArray();
        $this->valueKeys = false;
    }
}
