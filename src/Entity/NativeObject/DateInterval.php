<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use DecodeLabs\Nuance\Inspector;
use DecodeLabs\Nuance\Reflection;
use DateInterval as DateIntervalObject;

class DateInterval extends NativeObject
{
    public function __construct(
        DateIntervalObject $interval,
    ) {
        parent::__construct($interval);

        $this->itemName = Reflection::formatInterval($interval);

        Inspector::inspectClassMembers(
            object: $interval,
            entity: $this,
            asMeta: true,
        );
    }
}
