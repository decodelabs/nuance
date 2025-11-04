<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\FlagSet;
use SplFileObject as SplFileObjectObject;

class SplFileObject extends SplFileInfo
{
    public function __construct(
        SplFileObjectObject $file,
    ) {
        parent::__construct($file);

        $this->meta['eof'] = $file->eof();
        $this->meta['key'] = $file->key();
        $this->meta['flags'] = new FlagSet($file->getFlags(), [
            'SplFileObject::DROP_NEW_LINE',
            'SplFileObject::READ_AHEAD',
            'SplFileObject::SKIP_EMPTY',
            'SplFileObject::READ_CSV',
        ]);
    }
}
