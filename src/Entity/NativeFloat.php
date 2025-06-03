<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity;

class NativeFloat implements Value
{
    public float $value;

    public function __construct(
        float $value
    ) {
        $this->value = $value;
    }
}
