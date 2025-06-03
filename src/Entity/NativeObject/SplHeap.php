<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use SplHeap as SplHeapObject;

class SplHeap extends NativeObject
{
    /**
     * @template TValue
     * @param SplHeapObject<TValue> $heap
     */
    public function __construct(
        SplHeapObject $heap,
    ) {
        parent::__construct($heap);

        $this->length = count($heap);
        $this->values = iterator_to_array(clone $heap);
    }
}
