<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use ReflectionMethod as ReflectionMethodObject;

class ReflectionMethod extends ReflectionFunctionAbstract
{
    public function __construct(
        ReflectionMethodObject $reflection,
    ) {
        parent::__construct($reflection);

        $this->setProperty('class', $reflection->getDeclaringClass()->getName());
    }
}
