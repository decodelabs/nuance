<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use DOMElement as DOMElementObject;

class DOMElement extends NativeObject
{
    public function __construct(
        DOMElementObject $element,
    ) {
        parent::__construct($element);

        $this->itemName = $element->tagName;
        $this->setProperty('attributes', $element->attributes);
        $this->setProperty('childNodes', $element->childNodes);
    }
}
