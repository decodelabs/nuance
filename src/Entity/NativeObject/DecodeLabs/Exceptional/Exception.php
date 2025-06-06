<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject\DecodeLabs\Exceptional;

use DecodeLabs\Nuance\Entity\NativeObject\Throwable as ThrowableEntity;
use DecodeLabs\Exceptional\Exception as ExceptionObject;

class Exception extends ThrowableEntity
{
    public function __construct(
        ExceptionObject $exception,
    ) {
        parent::__construct($exception);

        $parts = [];

        if (!empty($exception->parameters->interfaces)) {
            $parts = $exception->parameters->interfaces;
        }

        if (
            isset($exception->parameters->type) &&
            $exception->parameters->type !== 'Exception'
        ) {
            $parts[] = $exception->parameters->type;
        }

        if (!empty($parts)) {
            foreach ($parts as $i => $part) {
                $inner = explode('\\', $part);
                $parts[$i] = array_pop($inner);

                if ($parts[$i] === 'Exception') {
                    unset($parts[$i]);
                }
            }

            $parts = array_unique($parts);
            $this->itemName = implode(' | ', $parts);
        }

        $this->text = $exception->getMessage();
        $this->displayName = '@Exceptional';

        $this->removeProperty('parameters');
        $this->valueKeys = false;


        if (null !== (
            $severity = $exception->parameters->severity
        )) {
            $defs = [];
            $constants = [
                'E_ERROR', 'E_WARNING', 'E_PARSE', 'E_NOTICE',
                'E_CORE_ERROR', 'E_CORE_WARNING', 'E_COMPILE_ERROR',
                'E_COMPILE_WARNING', 'E_USER_ERROR', 'E_USER_WARNING',
                'E_USER_NOTICE', 'E_RECOVERABLE_ERROR',
                'E_DEPRECATED', 'E_USER_DEPRECATED'
            ];

            foreach ($constants as $constant) {
                $value = constant($constant);

                if ($severity & $value) {
                    $defs[] = $constant;
                }
            }

            if (!empty($defs)) {
                $this->setProperty('severity', $severity, virtual: true);
            }
        }
    }
}
