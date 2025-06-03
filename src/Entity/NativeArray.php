<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity;

class NativeArray implements Value, Structured
{
    use StructuredTrait;

    private static ?string $cookieKey = null;

    /**
     * @var array<mixed>
     */
    public array $value;

    public int $length {
        get => count($this->value) - 1;
    }

    public ?string $hash {
        get {
            if (isset($this->hash)) {
                return $this->hash;
            }

            if(!$this->length) {
                return null;
            }

            $cookieKey = self::getCookieKey();
            $value = $this->value;
            unset($value[$cookieKey]);

            return md5(print_r($value, true));
        }
    }

    protected(set) bool $referenced = false;

    /**
     * @param array<mixed> $value
     */
    public function __construct(
        array &$value
    ) {
        $this->value = &$value;
        $cookieKey = self::getCookieKey();

        $this->referenced = array_key_exists($cookieKey, $this->value);

        if (!$this->referenced) {
            $this->value[$cookieKey] = $this->hash;
        }
    }

    public static function getCookieKey(): string
    {
        if(self::$cookieKey === null) {
            self::$cookieKey = uniqid('__nuance_array_');
        }

        return self::$cookieKey;
    }
}
