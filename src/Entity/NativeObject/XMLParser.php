<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use XMLParser as XMLParserObject;

class XMLParser extends NativeObject
{
    public function __construct(
        XMLParserObject $parser,
    ) {
        parent::__construct($parser);

        $this->meta = [
            'current_byte_index' => xml_get_current_byte_index($parser),
            'current_column_number' => xml_get_current_column_number($parser),
            'current_line_number' => xml_get_current_line_number($parser),
            'error_code' => xml_get_error_code($parser),
        ];
    }
}
