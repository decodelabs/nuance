<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use CurlHandle as CurlHandleObject;

class CurlHandle extends NativeObject
{
    public function __construct(
        CurlHandleObject $handle,
    ) {
        parent::__construct($handle);

        $this->meta = curl_getinfo($handle);
    }
}
