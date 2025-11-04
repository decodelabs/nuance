<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity;

class NativeInteger implements Value
{
    public int $value;

    public function __construct(
        int $value
    ) {
        $this->value = $value;
    }
}
