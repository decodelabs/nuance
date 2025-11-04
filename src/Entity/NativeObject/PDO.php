<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Coercion;
use DecodeLabs\Nuance\Entity\NativeObject;
use PDO as PDOObject;
use Throwable;

class PDO extends NativeObject
{
    private const Attributes = [
        'AUTOCOMMIT',
        'PREFETCH',
        'TIMEOUT',
        'ERRMODE',
        'SERVER_VERSION',
        'CLIENT_VERSION',
        'SERVER_INFO',
        'CONNECTION_STATUS',
        'CASE',
        'CURSOR_NAME',
        'CURSOR',
        'DRIVER_NAME',
        'ORACLE_NULLS',
        'PERSISTENT',
        'STATEMENT_CLASS',
        'FETCH_CATALOG_NAMES',
        'FETCH_TABLE_NAMES',
        'STRINGIFY_FETCHES',
        'MAX_COLUMN_LEN',
        'DEFAULT_FETCH_MODE',
        'EMULATE_PREPARES',
        'DEFAULT_STR_PARAM'
    ];

    public function __construct(
        PDOObject $pdo,
    ) {
        parent::__construct($pdo);

        foreach (self::Attributes as $name) {
            try {
                $this->meta[$name] = $pdo->getAttribute(
                    Coercion::asInt(constant('PDO::ATTR_' . $name))
                );
            } catch (Throwable $e) {
            }
        }

        $this->meta['availableDrivers'] = $pdo->getAvailableDrivers();

        $this->setProperty('inTransaction', $pdo->inTransaction(), 'private', virtual: true);
        $this->setProperty('lastInsertId', $pdo->lastInsertId(), 'private', virtual: true);
    }
}
