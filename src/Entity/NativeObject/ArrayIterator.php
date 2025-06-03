<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\FlagSet;
use DecodeLabs\Nuance\Entity\NativeObject;
use ArrayIterator as ArrayIteratorObject;

class ArrayIterator extends NativeObject
{
    /**
     * @template TKey of int|string
     * @template TValue
     * @param ArrayIteratorObject<TKey,TValue> $iterator
     */
    public function __construct(
        ArrayIteratorObject $iterator,
    ) {
        parent::__construct($iterator);

        $this->meta['flags'] = new FlagSet($iterator->getFlags(), [
            'ArrayIterator::STD_PROP_LIST',
            'ArrayIterator::ARRAY_AS_PROPS'
        ]);

        $this->values = $arr = $iterator->getArrayCopy();
        $this->length = count($arr);
    }
}
