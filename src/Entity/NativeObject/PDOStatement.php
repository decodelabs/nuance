<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\NativeObject;
use PDOStatement as PDOStatementObject;

class PDOStatement extends NativeObject
{
    public function __construct(
        PDOStatementObject $statement,
    ) {
        parent::__construct($statement);

        ob_start();
        $statement->debugDumpParams();

        if (false === ($dump = ob_get_clean())) {
            $dump = null;
        }

        $this->text = $dump;
        $this->definition = $statement->queryString;
        $this->length = $statement->columnCount();
    }
}
