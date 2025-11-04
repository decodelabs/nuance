<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\FlagSet;
use DecodeLabs\Nuance\Entity\NativeObject;
use SplDoublyLinkedList as SplDoublyLinkedListObject;

class SplDoublyLinkedList extends NativeObject
{
    /**
     * @template TValue
     * @param SplDoublyLinkedListObject<TValue> $list
     */
    public function __construct(
        SplDoublyLinkedListObject $list,
    ) {
        parent::__construct($list);

        $this->meta['iteratorMode'] = new FlagSet($list->getIteratorMode(), [
            'SplDoublyLinkedList::IT_MODE_LIFO',
            'SplDoublyLinkedList::IT_MODE_FIFO',
            'SplDoublyLinkedList::IT_MODE_DELETE',
            'SplDoublyLinkedList::IT_MODE_KEEP'
        ]);

        $this->length = count($list);

        $this->values = iterator_to_array(clone $list);
    }
}
