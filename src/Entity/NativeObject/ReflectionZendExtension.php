<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use ReflectionZendExtension as ReflectionZendExtensionObject;

class ReflectionZendExtension extends NativeObject
{
    public function __construct(
        ReflectionZendExtensionObject $extension,
    ) {
        parent::__construct($extension);

        $this->itemName = $extension->getName();

        $this->meta = [
            'version' => $extension->getVersion(),
            'author' => $extension->getAuthor(),
            'copyright' => $extension->getCopyright(),
            'url' => $extension->getURL()
        ];

        $this->open = false;
    }
}
