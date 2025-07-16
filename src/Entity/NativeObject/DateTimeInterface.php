<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DateTime;
use DateTimeInterface as DateTimeInterfaceObject;
use DecodeLabs\Nuance\Entity\NativeObject;
use DecodeLabs\Nuance\Reflection;

class DateTimeInterface extends NativeObject
{
    public function __construct(
        DateTimeInterfaceObject $date,
    ) {
        parent::__construct($date);

        $fromNow = (new DateTime())->diff($date);
        $this->itemName = $date->format('Y-m-d H:i:s T');

        $this->meta = [
            'w3c' => $date->format($date::W3C),
            'timezone' => $date->format('e'),
            'utc' => $date->format('P'),
            'timestamp' => $date->getTimestamp(),
            'fromNow' => Reflection::formatInterval($fromNow),
        ];
    }
}
