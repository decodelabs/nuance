<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use DOMDocumentType as DOMDocumentTypeObject;

class DOMDocumentType extends NativeObject
{
    public function __construct(
        DOMDocumentTypeObject $type,
    ) {
        parent::__construct($type);

        if (null !== ($owner = $type->ownerDocument)) {
            $this->definition = (string)$owner->saveXML($type);
        }

        $this->setProperty('name', $type->name);
        $this->setProperty('entities', $type->entities);
        $this->setProperty('notations', $type->notations);
        $this->setProperty('publicId', $type->publicId);
        $this->setProperty('systemId', $type->systemId);
        $this->setProperty('internalSubset', $type->internalSubset);
    }
}
