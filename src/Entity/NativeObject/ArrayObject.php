<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use ArrayObject as ArrayObjectObject;
use DecodeLabs\Nuance\Entity\FlagSet;
use DecodeLabs\Nuance\Entity\NativeObject;

class ArrayObject extends NativeObject
{
    /**
     * @template TKey of int|string
     * @template TValue
     * @param ArrayObjectObject<TKey,TValue> $object
     */
    public function __construct(
        ArrayObjectObject $object,
    ) {
        parent::__construct($object);

        $this->meta['flags'] = new FlagSet($object->getFlags(), [
            'ArrayObject::STD_PROP_LIST',
            'ArrayObject::ARRAY_AS_PROPS'
        ]);

        $this->values = $arr = $object->getArrayCopy();
        $this->length = count($arr);
    }
}
