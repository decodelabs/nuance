<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use DOMAttr as DOMAttrObject;

class DOMAttr extends NativeObject
{
    public function __construct(
        DOMAttrObject $attr,
    ) {
        parent::__construct($attr);

        $this->setProperty(
            name: 'name',
            value: $attr->name
        );

        $this->value = $attr->value;
    }
}
