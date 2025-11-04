<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use DecodeLabs\Nuance\Reflection;
use Generator as GeneratorObject;
use ReflectionGenerator;

class Generator extends NativeObject
{
    /**
     * @template TKey
     * @template TValue
     * @template TSend
     * @template TReturn
     * @param GeneratorObject<TKey,TValue,TSend,TReturn> $generator
     */
    public function __construct(
        GeneratorObject $generator,
    ) {
        parent::__construct($generator);

        try {
            $reflection = new ReflectionGenerator($generator)->getFunction();
        } catch (Exception $e) {
            return;
        }

        $this->definition = Reflection::getFunctionDefinition($reflection);
        $this->file = $reflection->getFileName() ?: null;
        $this->startLine = $reflection->getStartLine() ?: null;
        $this->endLine = $reflection->getEndLine() ?: null;
    }
}
