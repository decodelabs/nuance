<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use DecodeLabs\Nuance\Reflection;
use ReflectionParameter as ReflectionParameterObject;

class ReflectionParameter extends NativeObject
{
    public function __construct(
        ReflectionParameterObject $reflection,
    ) {
        parent::__construct($reflection);

        $this->itemName = '$' . $reflection->getName();
        $this->definition = Reflection::getParameterDefinition($reflection);
        $this->open = false;
    }
}
