<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use DecodeLabs\Nuance\Reflection;
use ReflectionClass as ReflectionClassObject;

class ReflectionClass extends NativeObject
{
    /**
     * @template T of object
     * @param ReflectionClassObject<T> $reflection
     */
    public function __construct(
        ReflectionClassObject $reflection,
    ) {
        parent::__construct($reflection);

        $this->definition = Reflection::getClassDefinition($reflection);
        $this->itemName = $reflection->getShortName();

        if (!$reflection->isInternal()) {
            $this->file = $reflection->getFileName() ?: null;
            $this->startLine = $reflection->getStartLine() ?: null;
            $this->endLine = $reflection->getEndLine() ?: null;
        }

        $this->open = false;
    }
}
