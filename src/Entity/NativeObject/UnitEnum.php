<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use BackedEnum;
use DecodeLabs\Nuance\Entity\NativeObject;
use UnitEnum as UnitEnumObject;

class UnitEnum extends NativeObject
{
    public function __construct(
        UnitEnumObject $enum,
    ) {
        parent::__construct($enum);

        $this->itemName = $enum->name;

        if($enum instanceof BackedEnum) {
            if(is_int($enum->value)) {
                $this->length = $enum->value;
            } else {
                $this->values[0] = $enum->value;
                $this->valueKeys = false;
            }
        }
    }
}
