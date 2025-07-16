<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use DecodeLabs\Nuance\Reflection;
use ReflectionFunctionAbstract as ReflectionFunctionAbstractObject;

class ReflectionFunctionAbstract extends NativeObject
{
    public function __construct(
        ReflectionFunctionAbstractObject $reflection,
    ) {
        parent::__construct($reflection);

        $this->definition = Reflection::getFunctionDefinition($reflection);
        $this->itemName = $reflection->getShortName();

        if (str_starts_with($this->itemName, '{closure')) {
            $this->itemName = 'closure';
        } else {
            $this->itemName .= '()';
        }

        $this->file = $reflection->getFileName() ?: null;
        $this->startLine = $reflection->getStartLine() ?: null;
        $this->endLine = $reflection->getEndLine() ?: null;
        $this->open = false;
    }
}
