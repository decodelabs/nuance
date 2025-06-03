<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject\Ds;

use DecodeLabs\Coercion;
use DecodeLabs\Nuance\Entity\NativeObject;
use Ds\Map as DsMap;
use Ds\Pair;

class Map extends NativeObject
{
    /**
     * @param DsMap<mixed,mixed> $map
     */
    public function __construct(
        DsMap $map,
    ) {
        parent::__construct($map);

        foreach ($map as $key => $value) {
            if(null !== ($stringKey = Coercion::tryString($key))) {
                $this->values[$stringKey] = $value;
            } else {
                $this->values[] = new Pair($key, $value);
            }
        }
    }
}
