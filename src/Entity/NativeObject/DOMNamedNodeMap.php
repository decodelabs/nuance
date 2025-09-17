<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use DOMNamedNodeMap as DOMNamedNodeMapObject;
use DOMNode;

class DOMNamedNodeMap extends NativeObject
{
    /**
     * @param DOMNamedNodeMapObject<DOMNode> $map
     */
    public function __construct(
        DOMNamedNodeMapObject $map,
    ) {
        parent::__construct($map);

        /** @var string $key */
        foreach ($map as $key => $attr) {
            $this->values[$key] = $attr;
        }

        $this->length = $map->length;
    }
}
