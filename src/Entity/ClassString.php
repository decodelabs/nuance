<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity;

class ClassString implements Value
{
    public string $value;

    public string $type {
        get {
            if(trait_exists($this->value)) {
                return 'trait';
            }

            if(interface_exists($this->value)) {
                return 'interface';
            }

            return 'class';
        }
    }

    public function __construct(
        string $value
    ) {
        $this->value = $value;
    }
}
