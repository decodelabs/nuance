<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use GdImage as GdImageObject;

class GdImage extends NativeObject
{
    public function __construct(
        GdImageObject $image,
    ) {
        parent::__construct($image);

        $this->meta = [
            'width' => imagesx($image),
            'height' => imagesy($image),
        ];
    }
}
