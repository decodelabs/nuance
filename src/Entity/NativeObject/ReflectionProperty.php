<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use DecodeLabs\Nuance\Reflection;
use ReflectionProperty as ReflectionPropertyObject;

class ReflectionProperty extends NativeObject
{
    public function __construct(
        ReflectionPropertyObject $reflection,
    ) {
        parent::__construct($reflection);

        $this->definition = Reflection::getPropertyDefinition($reflection);
        $this->itemName = '->' . $reflection->getName();
        $this->open = false;

        $this->setProperty('class', $reflection->getDeclaringClass()->getName());
    }
}
