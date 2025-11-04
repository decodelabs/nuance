<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use DOMText as DOMTextObject;

class DOMText extends NativeObject
{
    public function __construct(
        DOMTextObject $text,
    ) {
        parent::__construct($text);

        $this->text = $text->wholeText;
    }
}
