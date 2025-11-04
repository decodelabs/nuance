<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DateInterval as DateIntervalObject;
use DecodeLabs\Nuance\Entity\NativeObject;
use DecodeLabs\Nuance\Inspector;
use DecodeLabs\Nuance\Reflection;

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
