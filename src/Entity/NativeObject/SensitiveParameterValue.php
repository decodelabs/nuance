<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use SensitiveParameterValue as SensitiveParameterValueObject;

class SensitiveParameterValue extends NativeObject
{
    public function __construct(
        SensitiveParameterValueObject $value,
    ) {
        parent::__construct($value);

        $this->displayName = 'sensitive';
        $this->sensitive = true;
        $this->itemName = getType($value->getValue());
    }
}
