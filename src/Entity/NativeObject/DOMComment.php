<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use DOMComment as DOMCommentObject;

class DOMComment extends NativeObject
{
    public function __construct(
        DOMCommentObject $object,
    ) {
        parent::__construct($object);

        $this->text = $object->data;
    }
}
