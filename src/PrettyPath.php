<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance;

use DecodeLabs\Monarch;

class PrettyPath
{
    public function __construct(
        protected string $path
    ) {
    }

    public function __toString(): string
    {
        if (class_exists(Monarch::class)) {
            return Monarch::getPaths()->prettify($this->path);
        }

        return $this->path;
    }
}
