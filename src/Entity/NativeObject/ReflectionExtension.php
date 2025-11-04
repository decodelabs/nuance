<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use ReflectionExtension as ReflectionExtensionObject;

class ReflectionExtension extends NativeObject
{
    public function __construct(
        ReflectionExtensionObject $reflection,
    ) {
        parent::__construct($reflection);

        $this->itemName = $reflection->getName();

        $this->meta = [
            'version' => $reflection->getVersion(),
            'dependencies' => $reflection->getDependencies(),
            'iniEntries' => $reflection->getIniEntries(),
            'isPersistent' => $reflection->isPersistent(),
            'isTemporary' => $reflection->isTemporary(),
        ];

        $this->open = false;
    }
}
