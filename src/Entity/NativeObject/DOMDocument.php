<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use DOMDocument as DOMDocumentObject;

class DOMDocument extends NativeObject
{
    public function __construct(
        DOMDocumentObject $document,
    ) {
        parent::__construct($document);

        $this->definition = (string)$document->saveXML();

        $this->meta = [
            'encoding' => $document->encoding,
            'xmlEncoding' => $document->xmlEncoding,
            'xmlStandalone' => $document->xmlStandalone,
            'xmlVersion' => $document->xmlVersion,
            'formatOutput' => $document->formatOutput,
            'validateOnParse' => $document->validateOnParse,
            'resolveExternals' => $document->resolveExternals,
            'preserveWhiteSpace' => $document->preserveWhiteSpace,
            'recover' => $document->recover,
            'substituteEntities' => $document->substituteEntities
        ];

        $this->setProperty('documentURI', $document->documentURI);
        $this->setProperty('doctype', $document->doctype);
        $this->setProperty('documentElement', $document->documentElement);
    }
}
