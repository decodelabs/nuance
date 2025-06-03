<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use DOMProcessingInstruction as DOMProcessingInstructionObject;

class DOMProcessingInstruction extends NativeObject
{
    public function __construct(
        DOMProcessingInstructionObject $pi,
    ) {
        parent::__construct($pi);

        $this->definition = $pi->data;
    }
}
