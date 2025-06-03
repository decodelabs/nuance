<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity;

class Binary extends NativeString implements Structured
{
    use StructuredTrait;

    public function getHex(): string
    {
        return bin2hex($this->value);
    }
}
