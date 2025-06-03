<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use DecodeLabs\Nuance\Inspector;
use DateTimeZone as DateTimeZoneObject;

class DateTimeZone extends NativeObject
{
    public function __construct(
        DateTimeZoneObject $timeZone,
    ) {
        parent::__construct($timeZone);

        $this->itemName = $timeZone->getName();

        Inspector::inspectClassMembers(
            object: $timeZone,
            entity: $this,
            blackList: ['timezone'],
            asMeta: true
        );
    }
}
