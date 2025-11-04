<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject\DecodeLabs\Remnant;

use DecodeLabs\Nuance\Entity\NativeObject;
use DecodeLabs\Nuance\Entity\Traceable;
use DecodeLabs\Remnant\Trace as TraceObject;

class Trace extends NativeObject implements Traceable
{
    public protected(set) TraceObject $stackTrace;

    public function __construct(
        TraceObject $trace,
    ) {
        parent::__construct($trace);

        $this->stackTrace = $trace;
    }
}
