<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Structure;

use DecodeLabs\Enumerable\Backed\NamedString;
use DecodeLabs\Enumerable\Backed\NamedStringTrait;

enum ListStyle: string implements NamedString
{
    use NamedStringTrait;

    case Info = 'info';
    case Meta = 'meta';
    case Props = 'props';
    case Values = 'values';
}
