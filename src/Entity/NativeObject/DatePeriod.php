<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DatePeriod as DatePeriodObject;
use DateTimeInterface;
use DecodeLabs\Nuance\Entity\NativeObject;
use DecodeLabs\Nuance\Inspector;
use DecodeLabs\Nuance\Reflection;

class DatePeriod extends NativeObject
{
    /**
     * @param DatePeriodObject<DateTimeInterface,?DateTimeInterface,int> $period
     */
    public function __construct(
        DatePeriodObject $period,
    ) {
        parent::__construct($period);

        $this->text = sprintf(
            "every %s\nfrom %s%s\n%s",
            Reflection::formatInterval($period->getDateInterval(), false),
            $period->getStartDate()->format('Y-m-d H:i:s'),
            $period->include_start_date ? ' inc' : '',
            null !== ($end = $period->getEndDate()) ?
                'to ' . $end->format('Y-m-d H:i:s') :
                $period->recurrences . ' time(s)'
        );

        Inspector::inspectClassMembers(
            object: $period,
            entity: $this,
            asMeta: true,
        );
    }
}
