<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use DecodeLabs\Nuance\Reflection;
use ReflectionClassConstant as ReflectionClassConstantObject;

class ReflectionClassConstant extends NativeObject
{
    public function __construct(
        ReflectionClassConstantObject $reflection,
    ) {
        parent::__construct($reflection);

        $this->definition = Reflection::getConstantDefinition($reflection);
        $this->itemName = $reflection->getName();

        $this->setProperty('class', $reflection->class);
        $this->open = false;
    }
}
