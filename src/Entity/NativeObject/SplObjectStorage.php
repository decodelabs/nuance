<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use SplObjectStorage as SplObjectStorageObject;

class SplObjectStorage extends NativeObject
{
    /**
     * @template TObject of object
     * @template TData
     * @param SplObjectStorageObject<TObject,TData> $storage
     */
    public function __construct(
        SplObjectStorageObject $storage,
    ) {
        parent::__construct($storage);

        $this->length = $storage->count();
        $this->valueKeys = false;

        foreach (clone $storage as $object) {
            $this->values[] = [
                'object' => $object,
                'info' => $storage->getInfo()
            ];
        }
    }
}
