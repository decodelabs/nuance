<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject\DecodeLabs\Veneer\Plugin;

use DecodeLabs\Nuance\Entity\NativeObject;
use DecodeLabs\Veneer\Plugin\Wrapper as WrapperObject;

class Wrapper extends NativeObject
{
    public function __construct(
        // @phpstan-ignore-next-line
        WrapperObject $wrapper,
    ) {
        parent::__construct($wrapper);

        $this->displayName = '@PluginWrapper';
        // @phpstan-ignore-next-line
        $this->value = $wrapper->getVeneerPlugin();
    }
}
