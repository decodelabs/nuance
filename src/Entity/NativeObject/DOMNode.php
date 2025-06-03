<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\ConstOption;
use DecodeLabs\Nuance\Entity\NativeObject;

use DOMNode as DOMNodeObject;

class DOMNode extends NativeObject
{
    public function __construct(
        DOMNodeObject $node,
    ) {
        parent::__construct($node);

        $this->setProperty('nodeName', $node->nodeName);
        $this->setProperty('nodeType', new ConstOption($node->nodeType, [
            'XML_ELEMENT_NODE',
            'XML_ATTRIBUTE_NODE',
            'XML_TEXT_NODE',
            'XML_CDATA_SECTION_NODE',
            'XML_ENTITY_REF_NODE',
            'XML_ENTITY_NODE',
            'XML_PI_NODE',
            'XML_COMMENT_NODE',
            'XML_DOCUMENT_NODE',
            'XML_DOCUMENT_TYPE_NODE',
            'XML_DOCUMENT_FRAG_NODE',
            'XML_NOTATION_NODE',
            'XML_HTML_DOCUMENT_NODE',
            'XML_DTD_NODE',
            'XML_ELEMENT_DECL_NODE',
            'XML_ATTRIBUTE_DECL_NODE',
            'XML_ENTITY_DECL_NODE',
            'XML_NAMESPACE_DECL_NODE'
        ]));

        $this->values[0] = $node->nodeValue;
        $this->valueKeys = false;
    }
}
