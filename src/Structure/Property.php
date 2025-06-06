<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Structure;

class Property
{
    public function __construct(
        public string $name,
        public mixed $value,
        public PropertyVisibility $visibility = PropertyVisibility::Public,
        public bool $virtual = false,
        public bool $readOnly = false,
        public bool $open = true
    ) {}
}
