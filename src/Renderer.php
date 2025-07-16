<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance;

use DecodeLabs\Nuance\Entity\Binary;
use DecodeLabs\Nuance\Entity\ClassString;
use DecodeLabs\Nuance\Entity\ConstOption;
use DecodeLabs\Nuance\Entity\FlagSet;
use DecodeLabs\Nuance\Entity\NativeArray;
use DecodeLabs\Nuance\Entity\NativeBoolean;
use DecodeLabs\Nuance\Entity\NativeFloat;
use DecodeLabs\Nuance\Entity\NativeInteger;
use DecodeLabs\Nuance\Entity\NativeObject;
use DecodeLabs\Nuance\Entity\NativeResource;
use DecodeLabs\Nuance\Entity\NativeString;
use DecodeLabs\Nuance\Structure\ClassList;
use DecodeLabs\Nuance\Structure\Container;
use DecodeLabs\Nuance\Structure\ListStyle;
use DecodeLabs\Remnant\Frame;
use DecodeLabs\Remnant\Trace;

interface Renderer
{
    public const int Spaces = 2;

    #----------------------------- Grammar ----------------------------

    public function renderGrammar(
        string $grammar
    ): string;

    public function renderPointer(
        string $pointer
    ): string;



    #----------------------------- Types ----------------------------



    public function renderValue(
        mixed $value,
        int $level = 0,
        ?ClassList $classes = null,
    ): string;

    public function renderNull(
        ?ClassList $classes = null
    ): string;

    public function renderBoolean(
        NativeBoolean $entity,
        ?ClassList $classes = null
    ): string;

    public function renderInteger(
        NativeInteger $entity,
        ?ClassList $classes = null
    ): string;

    public function renderFloat(
        NativeFloat $entity,
        ?ClassList $classes = null
    ): string;

    public function renderIdentifier(
        string $identifier,
        ?ClassList $classes = null,
        ?int $singleLineMax = null
    ): string;

    public function renderString(
        NativeString $entity,
        ?ClassList $classes = null,
        ?int $singleLineMax = null
    ): string;

    public function renderSingleLineString(
        NativeString $entity,
        ?ClassList $classes = null,
        ?int $singleLineMax = null
    ): string;

    public function renderMultiLineString(
        NativeString $entity,
        ?ClassList $classes = null,
    ): string;


    public function renderStringLine(
        string $line,
        ?int $maxLength = null
    ): string;

    public function renderBinary(
        Binary $entity,
        ?ClassList $classes = null,
    ): string;

    public function renderClassString(
        ClassString $entity,
        ?ClassList $classes = null,
    ): string;

    public function renderClassName(
        string $class,
        ?ClassList $classes = null,
    ): string;

    public function renderControlCharacter(
        string $control
    ): string;


    public function renderResource(
        NativeResource $entity,
        ?ClassList $classes = null,
    ): string;



    public function renderConstOption(
        ConstOption $entity,
        ?ClassList $classes = null,
    ): string;

    public function renderFlagSet(
        FlagSet $entity,
        ?ClassList $classes = null,
    ): string;

    public function renderArray(
        NativeArray $entity,
        int $level = 0,
        ?ClassList $classes = null,
    ): string;

    public function renderObject(
        NativeObject $entity,
        int $level = 0,
        ?ClassList $classes = null,
    ): string;

    public function renderObjectReference(
        NativeObject $entity,
        ?ClassList $classes = null,
    ): string;



    public function renderContainer(
        Container $container,
    ): string;



    #----------------------------- Stack Frames ----------------------------



    public function renderStackTrace(
        Trace $trace,
        ?ClassList $classes = null,
    ): string;

    public function renderStackFrameNumber(
        int $number,
        ?ClassList $classes = null,
    ): string;

    public function renderStackFrameSignature(
        Frame $frame
    ): string;

    public function renderStackFrameLocation(
        ?string $file,
        ?int $line = null,
        ?ClassList $classes = null,
    ): string;

    public function renderStackFrameFile(
        string $file,
        ?ClassList $classes = null,
    ): string;

    public function renderStackFrameLine(
        int $line,
        ?ClassList $classes = null,
    ): string;

    public function renderStackFrameSource(
        Frame $frame
    ): ?string;


    #----------------------------- Signatures ----------------------------


    public function wrapSignature(
        string $signature,
        ?ClassList $classes = null,
    ): string;

    public function renderSignatureFqn(
        string $class,
        ?ClassList $classes = null,
    ): string;

    public function renderSignatureNamespace(
        string $namespace,
        ?ClassList $classes = null,
    ): string;

    public function renderSignatureClassName(
        string $class,
        ?ClassList $classes = null,
    ): string;

    public function renderSignatureConstant(
        string $constant,
        ?ClassList $classes = null,
    ): string;

    public function wrapSignatureFunction(
        string $function,
        ?ClassList $classes = null,
    ): string;

    public function renderSignatureClosure(
        ?ClassList $classes = null,
    ): string;

    public function wrapSignatureArray(
        string $array,
        ?ClassList $classes = null,
    ): string;

    public function renderSignatureObject(
        string $object,
        ?ClassList $classes = null,
    ): string;

    /**
     * @param resource $resource
     */
    public function renderSignatureResource(
        $resource,
        ?ClassList $classes = null,
    ): string;

    public function renderConstName(
        string $const,
        ?ClassList $classes = null,
    ): string;




    #----------------------------- Lists ----------------------------



    /**
     * @param array<int|string,mixed> $items
     */
    public function renderList(
        array $items,
        string|ListStyle $style,
        bool $includeKeys = true,
        ?ClassList $classes = null,
        int $level = 0
    ): string;

    /**
     * @param array<string> $lines
     */
    public function renderListStructure(
        array $lines,
        ?ClassList $classes = null,
    ): string;



    #----------------------------- Utils ----------------------------


    public function indent(
        string $lines
    ): string;

    public function escape(
        ?string $string
    ): string;
}
