<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use Throwable;
use XMLWriter as XMLWriterObject;

class XMLWriter extends NativeObject
{
    public function __construct(
        XMLWriterObject $writer,
    ) {
        parent::__construct($writer);

        try {
            $this->text = $writer->outputMemory(false);
        } catch (Throwable $e) {
        }
    }
}
