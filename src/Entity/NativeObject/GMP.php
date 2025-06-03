<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use GMP as GMPObject;

class GMP extends NativeObject
{
    public function __construct(
        GMPObject $number,
    ) {
        parent::__construct($number);

        $this->text = gmp_strval($number);
    }
}
