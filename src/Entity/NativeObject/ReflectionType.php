<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use DecodeLabs\Nuance\Reflection;
use ReflectionType as ReflectionTypeObject;
use ReflectionNamedType;
use ReflectionUnionType;

class ReflectionType extends NativeObject
{
    public function __construct(
        ReflectionTypeObject $reflection,
    ) {
        parent::__construct($reflection);

        $this->itemName = Reflection::getTypeName($reflection, short: true);
        $this->setProperty('name', Reflection::getTypeName($reflection), virtual: true);

        if ($reflection instanceof ReflectionNamedType) {
            $this->setProperty('isBuiltin', $reflection->isBuiltin(), virtual: true);
        }

        $this->setProperty('allowsNull', $reflection->allowsNull(), virtual: true);
        $this->open = false;
    }
}
