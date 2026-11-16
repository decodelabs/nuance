<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Tests;

use DecodeLabs\Nuance\Entity\NativeArray;
use DecodeLabs\Nuance\Entity\NativeObject;
use DecodeLabs\Nuance\Inspector;
use DecodeLabs\Nuance\Renderer\Html;
use ErrorException;
use RuntimeException;
use stdClass;

require dirname(__DIR__) . '/vendor/autoload.php';

set_error_handler(static function (
    int $severity,
    string $message,
    string $file,
    int $line
): never {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

try {
    $inspector = new Inspector();
    $empty = $inspector->inspect([]);
    $repeated = $inspector->inspect([]);
    $populated = $inspector->inspect(['value' => null]);
    $same = $inspector->inspect(['value' => null]);

    if (
        !$empty instanceof NativeArray ||
        !$repeated instanceof NativeArray ||
        !$populated instanceof NativeArray ||
        !$same instanceof NativeArray
    ) {
        throw new RuntimeException('Arrays must produce array entities');
    }

    if ($empty->hash !== null || $empty->getPreparedValue() !== []) {
        throw new RuntimeException('Empty array representation must remain unchanged');
    }

    if (
        $empty->referenceId !== $repeated->referenceId ||
        $populated->referenceId !== $same->referenceId ||
        $empty->referenceId === $populated->referenceId
    ) {
        throw new RuntimeException('Array reference IDs must remain consistent and distinct');
    }

    $object = new NativeObject(new stdClass());

    foreach ([[], ['' => 'empty key'], ['first' => null, 'next' => 'later'], [3 => false, 1 => 'later']] as $values) {
        $object->values = $values;
        end($object->values);
        $key = key($object->values);

        if ($object->value !== (array_values($values)[0] ?? null)) {
            throw new RuntimeException('Object entities must return the first inserted value or null');
        }

        if (key($object->values) !== $key) {
            throw new RuntimeException('Reading object values must preserve the array pointer');
        }
    }

    $renderer = new Html();

    foreach ([[], ['empty' => [], 'value' => null], [[], []]] as $value) {
        if ($renderer->renderValue($value) === '') {
            throw new RuntimeException('Empty and nested arrays must render without warnings');
        }
    }

    $inspector->reset();
    $reset = $inspector->inspect([]);

    if (!$reset instanceof NativeArray || $reset->referenceId !== 0) {
        throw new RuntimeException('Reset must clear array reference IDs');
    }
} finally {
    restore_error_handler();
}

echo "Array inspection and rendering regression checks passed.\n";
