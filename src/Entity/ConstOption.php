<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity;

class ConstOption implements Value, Structured
{
    use StructuredTrait;

    public string|bool|int|float|null $value;

    /**
     * @var array<string,string|bool|int|float|null>
     */
    protected(set) array $options = [];

    /**
     * @param list<string> $constNames
     */
    public function __construct(
        string|bool|int|float|null $value,
        array $constNames = []
    ) {
        $this->value = $value;
        $this->options = [];

        foreach($constNames as $name) {
            $name = ltrim($name, '\\');

            // @phpstan-ignore-next-line
            $this->options[$name] = constant($name);
        }
    }

    public function getSelectedConstName(): ?string
    {
        if ($this->value === null) {
            return null;
        }

        foreach ($this->options as $name => $constValue) {
            if ($constValue === $this->value) {
                return $name;
            }
        }

        return null;
    }
}
