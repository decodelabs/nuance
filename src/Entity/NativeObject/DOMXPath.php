<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use DOMXPath as DOMXPathObject;

class DOMXPath extends NativeObject
{
    public function __construct(
        DOMXPathObject $xPath,
    ) {
        parent::__construct($xPath);

        $this->setProperty(
            'document',
            $xPath->document
        );
    }
}
