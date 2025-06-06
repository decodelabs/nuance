<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Structure;

class SectionMap
{
    /**
     * @var array<string,bool>
     */
    protected array $sections = [];

    public function __construct(
        bool ...$sections
    ) {
        foreach ($sections as $key => $open) {
            if(!is_string($key)) {
                continue;
            }

            $this->sections[$key] = $open;
        }
    }

    public function disable(
        string $key
    ): void {
        $this->sections[$key] = false;
    }

    public function enable(
        string $key
    ): void {
        $this->sections[$key] = true;
    }

    public function isEnabled(
        string $key
    ): bool {
        return $this->sections[$key] ?? true;
    }
}
