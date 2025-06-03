<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use SimpleXMLElement as SimpleXMLElementObject;
use ReflectionObject;

class SimpleXMLElement extends NativeObject
{
    public function __construct(
        SimpleXMLElementObject $element,
    ) {
        parent::__construct($element);

        $ref = new ReflectionObject($element);

        foreach($ref->getProperties() as $property) {
            $name = $property->getName();
            $this->values[$name] = $property->getValue($element);
        }

        $xml = $element->asXML();

        if (is_bool($xml)) {
            $xml = null;
        }

        $this->text = empty($this->values) ? (string)$element : null;
        $this->definition = $xml;
    }
}
