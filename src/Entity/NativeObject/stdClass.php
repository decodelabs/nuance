<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use stdClass as StdClassObject;

class stdClass extends NativeObject
{
    public function __construct(
        StdClassObject $object,
    ) {
        parent::__construct($object);

        $array = (array)$object;
        $this->length = count($array);

        foreach ($array as $key => $value) {
            $this->setProperty($key, $value);
        }
    }
}
