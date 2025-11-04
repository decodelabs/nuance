<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity;

class NativeBoolean implements Value
{
    public bool $value;

    public function __construct(
        bool $value
    ) {
        $this->value = $value;
    }
}
