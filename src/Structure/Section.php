<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Structure;

class Section
{
    public protected(set) string $key;

    public string $id {
        get => $this->id ??= uniqid('section-' . $this->key . '-');
    }

    public string $keyChar {
        get {
            if (!isset($this->keyChar)) {
                $this->keyChar = substr($this->key, 0, 1);
            }

            return $this->keyChar;
        }
    }

    public int $priority = 0;
    public bool $open = true;

    public string $renderedContent = '';

    public function __construct(
        string $key,
        int $priority = 0,
        bool $open = true
    ) {
        $this->key = $key;
        $this->priority = $priority;
        $this->open = $open;
    }
}
