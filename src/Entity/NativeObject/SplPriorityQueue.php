<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\FlagSet;
use DecodeLabs\Nuance\Entity\NativeObject;
use SplPriorityQueue as SplPriorityQueueObject;

class SplPriorityQueue extends NativeObject
{
    /**
     * @template TPriority
     * @template TValue
     * @param SplPriorityQueueObject<TPriority,TValue> $queue
     */
    public function __construct(
        SplPriorityQueueObject $queue,
    ) {
        parent::__construct($queue);

        $this->meta['extractFlags'] = new FlagSet($queue->getExtractFlags(), [
            'SplPriorityQueue::EXTR_DATA',
            'SplPriorityQueue::EXTR_PRIORITY',
            'SplPriorityQueue::EXTR_BOTH'
        ]);

        $this->length = $queue->count();
        $this->values = iterator_to_array(clone $queue);
    }
}
