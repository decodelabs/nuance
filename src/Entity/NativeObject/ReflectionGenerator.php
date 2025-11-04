<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use DecodeLabs\Nuance\Reflection;
use ReflectionGenerator as ReflectionGeneratorObject;

class ReflectionGenerator extends NativeObject
{
    public function __construct(
        ReflectionGeneratorObject $reflection,
    ) {
        parent::__construct($reflection);

        $this->definition = Reflection::getFunctionDefinition(
            $function = $reflection->getFunction()
        );

        $this->itemName = $function->getShortName() ?: null;

        if (str_starts_with((string)$this->itemName, '{closure')) {
            $this->itemName = 'closure';
        }

        $this->file = $function->getFileName() ?: null;
        $this->startLine = $function->getStartLine() ?: null;
        $this->endLine = $function->getEndLine() ?: null;
        $this->open = false;
    }
}
