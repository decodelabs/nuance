<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use __PHP_Incomplete_Class as __PHP_Incomplete_ClassObject;
use DecodeLabs\Coercion;

class __PHP_Incomplete_Class extends NativeObject
{
    public function __construct(
        __PHP_Incomplete_ClassObject $object,
    ) {
        parent::__construct($object);

        $vars = (array)$object;
        $this->definition = Coercion::tryString($vars['__PHP_Incomplete_Class_Name'] ?? null);
        unset($vars['__PHP_Incomplete_Class_Name']);
        $this->values = $vars;
    }
}
