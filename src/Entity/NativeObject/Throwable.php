<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Glitch\Stack\Trace;
use DecodeLabs\Nuance\Entity\NativeObject;
use DecodeLabs\Nuance\Entity\Traceable;
use DecodeLabs\Nuance\Inspector;
use Throwable as ThrowableObject;

class Throwable extends NativeObject implements Traceable
{
    protected(set) Trace $stackTrace;

    public function __construct(
        ThrowableObject $exception,
    ) {
        parent::__construct($exception);

        $this->text = $exception->getMessage();
        $this->file = $exception->getFile();
        $this->startLine = $exception->getLine();
        $this->stackTrace = Trace::fromException($exception);

        $this->setProperty(
            'code',
            $exception->getcode(),
            'private',
            readOnly: true
        );

        $this->setProperty(
            'previous',
            $exception->getPrevious(),
            'private'
        );

        Inspector::inspectClassMembers(
            object: $exception,
            entity: $this,
            blackList: [
                'code',
                'previous',
                'message',
                'file',
                'line',
                'trace',
                'stackTrace',
                'string',
                'xdebug_message'
            ]
        );
    }
}
