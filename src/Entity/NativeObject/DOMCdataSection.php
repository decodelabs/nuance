<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use DOMCdataSection as DOMCdataSectionObject;

class DOMCdataSection extends NativeObject
{
    public function __construct(
        DOMCdataSectionObject $cData,
    ) {
        parent::__construct($cData);

        $this->text = $cData->data;
    }
}
