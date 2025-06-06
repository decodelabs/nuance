<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Monarch;
use DecodeLabs\Nuance\Entity\NativeObject;
use DecodeLabs\Nuance\Entity\Traceable;
use DecodeLabs\Nuance\Inspector;
use DecodeLabs\Remnant\Trace;
use Throwable as ThrowableObject;

class Throwable extends NativeObject implements Traceable
{
    protected(set) Trace $stackTrace;

    public function __construct(
        ThrowableObject $exception,
    ) {
        parent::__construct($exception);

        $this->text = $exception->getMessage();
        $this->file = $file = $exception->getFile();
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
                'trace',
                'stackFrame',
                'stackTrace',
                'string',
                'xdebug_message'
            ]
        );

        if(class_exists(Monarch::class)) {
            $file = Monarch::$paths->prettify($file);
        }

        $this->setProperty('file', $file, 'private');
    }
}
