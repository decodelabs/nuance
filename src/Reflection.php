<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance;

use DateInterval;
use DateTime;
use DecodeLabs\Exceptional;
use Reflection as ReflectionRoot;
use ReflectionClass;
use ReflectionClassConstant;
use ReflectionFunctionAbstract;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;
use UnitEnum;

class Reflection
{
    /**
     * @template T of object
     * @param ReflectionClass<T> $reflection
     */
    public static function getClassDefinition(
        ReflectionClass $reflection
    ): string {
        $output = 'class ';
        $name = $reflection->getName();

        if (0 === strpos($name, "class@anonymous\x00")) {
            $output .= '() ';
        } else {
            $output .= $name . ' ';
        }

        if ($parent = $reflection->getParentClass()) {
            $output .= 'extends ' . $parent->getName();
        }

        $interfaces = [];

        foreach ($reflection->getInterfaces() as $interface) {
            $interfaces[] = $interface->getName();
        }

        if (!empty($interfaces)) {
            $output .= 'implements ' . implode(', ', $interfaces) . ' ';
        }

        $output .= '{' . "\n";

        foreach ($reflection->getReflectionConstants() as $const) {
            $output .= '    ' . self::getConstantDefinition($const) . "\n";
        }

        foreach ($reflection->getProperties() as $property) {
            $output .= '    ' . self::getPropertyDefinition($property) . "\n";
        }

        foreach ($reflection->getMethods() as $method) {
            $output .= '    ' . self::getFunctionDefinition($method) . "\n";
        }

        $output .= '}';

        return $output;
    }


    public static function getPropertyDefinition(
        ReflectionProperty $reflection
    ): string {
        $output = implode(' ', ReflectionRoot::getModifierNames($reflection->getModifiers()));
        $name = $reflection->getName();
        $output .= ' $' . $name . ' = ';
        $reflection->setAccessible(true);
        $props = $reflection->getDeclaringClass()->getDefaultProperties();
        $value = $props[$name] ?? null;
        $output .= self::renderStaticValue($value);

        return $output;
    }


    public static function getConstantDefinition(
        ReflectionClassConstant $reflection
    ): string {
        $output = implode(' ', ReflectionRoot::getModifierNames($reflection->getModifiers()));
        $output .= ' const ' . $reflection->getName() . ' = ';
        $value = $reflection->getValue();
        $output .= self::renderStaticValue($value);

        return $output;
    }


    public static function getFunctionDefinition(
        ReflectionFunctionAbstract $reflection
    ): string {
        $output = '';

        if ($reflection instanceof ReflectionMethod) {
            $output = implode(' ', ReflectionRoot::getModifierNames($reflection->getModifiers()));

            if (!empty($output)) {
                $output .= ' ';
            }
        }

        $output .= 'function ';

        if ($reflection->returnsReference()) {
            $output .= '& ';
        }

        if (!$reflection->isClosure()) {
            $output .= $reflection->getName() . ' ';
        }

        $output .= '(';
        $params = [];

        foreach ($reflection->getParameters() as $parameter) {
            $params[] = self::getParameterDefinition($parameter);
        }

        $output .= implode(', ', $params) . ')';

        if ($returnType = $reflection->getReturnType()) {
            $output .= ': ';

            if ($returnType->allowsNull()) {
                $output .= '?';
            }

            $output .= static::getTypeName($returnType);
        }

        return $output;
    }

    public static function getParameterDefinition(
        ReflectionParameter $parameter
    ): string {
        $output = '';

        if ($parameter->allowsNull()) {
            $output .= '?';
        }

        if ($type = $parameter->getType()) {
            $output .= static::getTypeName($type). ' ';
        }

        if ($parameter->isPassedByReference()) {
            $output .= '& ';
        }

        if ($parameter->isVariadic()) {
            $output .= '...';
        }

        $output .= '$' . $parameter->getName();

        if ($parameter->isDefaultValueAvailable()) {
            /** @var bool|float|int|resource|string|null $value */
            $value = $parameter->getDefaultValue();
            $output .= '=' . static::renderStaticValue($value);
        }

        return $output;
    }

    protected static function renderStaticValue(
        mixed $value
    ): string {
        if (is_array($value)) {
            return '[...]';
        }

        if ($value instanceof UnitEnum) {
            return get_class($value).'::'.$value->name;
        }

        if(!is_scalar($value)) {
            $value = gettype($value);
        }

        return static::scalarToString($value);
    }

    public static function getTypeName(
        ReflectionType $type,
        bool $short = false
    ): string {
        if ($type instanceof ReflectionNamedType) {
            $output = $type->getName();

            if($short) {
                $output = explode('\\', $output);
                $output = array_pop($output);
            }

            return $output;
        }

        if ($type instanceof ReflectionUnionType) {
            $parts = [];

            foreach ($type->getTypes() as $innerType) {
                $parts[] = static::getTypeName($innerType);
            }

            return implode('|', $parts);
        }

        if($type instanceof ReflectionIntersectionType) {
            $parts = [];

            foreach ($type->getTypes() as $innerType) {
                $parts[] = static::getTypeName($innerType);
            }

            return '('.implode('&', $parts).')';
        }

        return '';
    }

    /**
     * @param scalar|resource|null $value
     */
    protected static function scalarToString(
        $value
    ): string {
        switch (true) {
            case $value === null:
                return 'null';

            case is_bool($value):
                return $value ? 'true' : 'false';

            case is_int($value):
            case is_float($value):
                return (string)$value;

            case is_string($value):
                return '"' . $value . '"';

            case is_resource($value):
            default:
                return (string)$value;
        }
    }



    public static function formatInterval(
        DateInterval $interval,
        bool $nominal = true
    ): string {
        $format = '';

        if (
            $interval->y === 0 &&
            $interval->m === 0 &&
            (
                $interval->h >= 24 ||
                $interval->i >= 60 ||
                $interval->s >= 60
            )
        ) {
            $date1 = new DateTime();
            $date2 = clone $date1;

            /** @phpstan-ignore-next-line */
            if (false === $date2->add($interval)) {
                throw Exceptional::Runtime(
                    message: 'Unable to create date from interval'
                );
            }

            $interval = date_diff($date1, $date2);
            $format .= 0 < $interval->days ? '%ad ' : '';
        } else {
            if ($interval->y) {
                $format .= '%yy ';
            }

            if ($interval->m) {
                $format .= '%mm ';
            }

            if ($interval->d) {
                $format .= '%dd ';
            }
        }

        if ($interval->h || !empty($format)) {
            $format .= '%H:';
        }
        if ($interval->i || !empty($format)) {
            $format .= '%I:';
        }
        if ($interval->s || !empty($format)) {
            $format .= '%S';
        }

        $format = trim($format);

        if (empty($format)) {
            $format = '0s';
        }

        if ($nominal) {
            $format = '%R ' . $format;
        }

        return $interval->format($format);
    }


    public static function formatFileSize(
        int $bytes
    ): string {
        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
