<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use DecodeLabs\Nuance\Entity\Traceable;
use DecodeLabs\Remnant\Trace;
use Fiber as FiberObject;
use ReflectionFiber;

class Fiber extends NativeObject implements Traceable
{
    public protected(set) ?Trace $stackTrace = null;

    /**
     * @param FiberObject<mixed,mixed,mixed,mixed> $fiber
     */
    public function __construct(
        FiberObject $fiber,
    ) {
        parent::__construct($fiber);

        if (
            $fiber->isStarted() &&
            !$fiber->isTerminated()
        ) {
            $reflection = new ReflectionFiber($fiber);

            $this->file = $reflection->getExecutingFile();
            $this->startLine = $reflection->getExecutingLine();
            $this->stackTrace = Trace::fromDebugBacktrace($reflection->getTrace());
        }

        $this->meta = [
            'started' => $fiber->isStarted(),
            'running' => $fiber->isRunning(),
            'suspended' => $fiber->isSuspended(),
            'terminated' => $fiber->isTerminated(),
        ];

        if ($fiber->isTerminated()) {
            $this->value = $fiber->getReturn();
        }
    }
}
