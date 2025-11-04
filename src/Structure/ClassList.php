<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Structure;

use Stringable;

class ClassList implements Stringable
{
    /**
     * @var list<string>
     */
    public protected(set) array $classes = [];

    public static function fromString(
        ?string $classes
    ): ?self {
        if (
            $classes === null ||
            $classes === ''
        ) {
            return new self();
        }

        return new self(...explode(' ', $classes));
    }

    public static function of(
        string|self|null ...$classes
    ): self {
        $output = new self();

        foreach ($classes as $class) {
            if ($class === null) {
                continue;
            }

            if ($class instanceof self) {
                $output->add(...$class->toArray());
            } elseif (is_string($class)) {
                $output->add($class);
            }
        }

        return $output;
    }

    public function __construct(
        string ...$classes
    ) {
        $this->classes = array_values(array_unique($classes));
    }

    /**
     * @return $this
     */
    public function add(
        string ...$classes
    ): self {
        $this->classes = array_values(array_unique(array_merge($this->classes, $classes)));
        return $this;
    }

    /**
     * @return $this
     */
    public function remove(
        string ...$classes
    ): self {
        $this->classes = array_values(array_diff($this->classes, $classes));
        return $this;
    }

    public function has(
        string $class
    ): bool {
        return in_array($class, $this->classes, true);
    }

    public function isEmpty(): bool
    {
        return empty($this->classes);
    }

    /**
     * @return list<string>
     */
    public function toArray(): array
    {
        return $this->classes;
    }

    public function __toString(): string
    {
        return implode(' ', $this->classes);
    }
}
