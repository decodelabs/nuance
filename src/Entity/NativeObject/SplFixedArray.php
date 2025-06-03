<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use SplFixedArray as SplFixedArrayObject;

class SplFixedArray extends NativeObject
{
    /**
     * @template TValue
     * @param SplFixedArrayObject<TValue> $array
     */
    public function __construct(
        SplFixedArrayObject $array,
    ) {
        parent::__construct($array);

        $this->length = $array->getSize();
        $this->values = $array->toArray();
    }
}
