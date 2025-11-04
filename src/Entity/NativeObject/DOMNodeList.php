<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use DOMNode;
use DOMNodeList as DOMNodeListObject;

class DOMNodeList extends NativeObject
{
    /**
     * @param DOMNodeListObject<DOMNode> $list
     */
    public function __construct(
        DOMNodeListObject $list,
    ) {
        parent::__construct($list);

        $this->values = iterator_to_array($list, false);
        $this->valueKeys = false;

        $this->length = count($this->values);
        $this->open = false;
    }
}
