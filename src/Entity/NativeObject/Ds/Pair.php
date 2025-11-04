<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject\Ds;

use DecodeLabs\Nuance\Entity\NativeObject;
use Ds\Pair as DsPair;

class Pair extends NativeObject
{
    /**
     * @param DsPair<mixed,mixed> $pair
     */
    public function __construct(
        DsPair $pair,
    ) {
        parent::__construct($pair);

        foreach ($pair->toArray() as $key => $value) {
            $this->setProperty($key, $value);
        }
    }
}
