<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity;

class NativeString implements Value
{
    public string $value;

    public int $length {
        get => strlen($this->value);
    }

    public function __construct(
        string $value
    ) {
        $this->value = $value;
    }

    public function isMultiLine(): bool
    {
        return str_contains($this->value, "\n");
    }

    public function isMultiByte(): bool
    {
        return mb_strlen($this->value) !== $this->length;
    }
}
