<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject\Ds;

use DecodeLabs\Nuance\Entity\NativeObject;
use Ds\Collection as DsCollection;

class Collection extends NativeObject
{
    /**
     * @param DsCollection<mixed,mixed> $collection
     */
    public function __construct(
        DsCollection $collection,
    ) {
        parent::__construct($collection);

        $this->length = count($collection);
        $this->values = $collection->toArray();
    }
}
