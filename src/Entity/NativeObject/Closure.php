<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use Closure as ClosureObject;
use DecodeLabs\Nuance\Entity\NativeObject;
use DecodeLabs\Nuance\Reflection;
use ReflectionFunction;

class Closure extends NativeObject
{
    public function __construct(
        ClosureObject $closure,
    ) {
        parent::__construct($closure);

        $reflection = new ReflectionFunction($closure);

        $this->definition = Reflection::getFunctionDefinition($reflection);
        $this->file = $reflection->getFileName() ?: null;
        $this->startLine = $reflection->getStartLine() ?: null;
        $this->endLine = $reflection->getEndLine() ?: null;
    }
}
