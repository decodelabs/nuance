<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity;

use DecodeLabs\Coercion;

class FlagSet implements Value
{
    public int $value;

    /**
     * @var array<string,int>
     */
    public protected(set) array $options = [];

    /**
     * @param list<string> $constNames
     */
    public function __construct(
        int $value,
        array $constNames = []
    ) {
        $this->value = $value;
        $this->options = [];

        foreach ($constNames as $name) {
            $name = ltrim($name, '\\');
            $option = Coercion::tryInt(constant($name));

            if ($option === null) {
                continue;
            }

            $this->options[$name] = $option;
        }
    }

    /**
     * @return array<string,int>
     */
    public function getSelectedConstValues(): array
    {
        $output = [];

        foreach ($this->options as $name => $constValue) {
            if (
                (
                    $constValue !== 0 &&
                    ($this->value & $constValue) === $constValue
                ) ||
                (
                    $this->value === 0 &&
                    $constValue === 0
                )
            ) {
                $output[$name] = $constValue;
            }
        }

        return $output;
    }
}
