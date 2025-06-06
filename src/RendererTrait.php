<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance;

use DecodeLabs\Exceptional;
use DecodeLabs\Monarch;
use DecodeLabs\Nuance\Entity;
use DecodeLabs\Nuance\Entity\Binary;
use DecodeLabs\Nuance\Entity\ClassString;
use DecodeLabs\Nuance\Entity\ConstOption;
use DecodeLabs\Nuance\Entity\FlagSet;
use DecodeLabs\Nuance\Entity\NativeArray;
use DecodeLabs\Nuance\Entity\NativeBoolean;
use DecodeLabs\Nuance\Entity\NativeFloat;
use DecodeLabs\Nuance\Entity\NativeInteger;
use DecodeLabs\Nuance\Entity\NativeNull;
use DecodeLabs\Nuance\Entity\NativeResource;
use DecodeLabs\Nuance\Entity\NativeObject;
use DecodeLabs\Nuance\Entity\NativeObject\DecodeLabs\Remnant\Trace as TraceEntity;
use DecodeLabs\Nuance\Entity\NativeObject\Throwable as ThrowableEntity;
use DecodeLabs\Nuance\Entity\NativeString;
use DecodeLabs\Nuance\Entity\Traceable;
use DecodeLabs\Nuance\Structure\ClassList;
use DecodeLabs\Nuance\Structure\Container;
use DecodeLabs\Nuance\Structure\LazyType;
use DecodeLabs\Nuance\Structure\ListStyle;
use DecodeLabs\Nuance\Structure\Property;
use DecodeLabs\Nuance\Structure\Section;
use DecodeLabs\Remnant\Frame;
use DecodeLabs\Remnant\Trace;

/**
 * @phpstan-require-implements Renderer
 */
trait RendererTrait
{
    protected Inspector $inspector;

    public function __construct()
    {
        $this->inspector = new Inspector();
    }


    #----------------------------- Grammar ----------------------------



    public function renderGrammar(
        string $grammar
    ): string {
        return $grammar;
    }

    public function renderPointer(
        string $pointer
    ): string {
        return $pointer;
    }



    #----------------------------- Types ----------------------------



    public function renderValue(
        mixed $value,
        int $level = 0,
        ?ClassList $classes = null
    ): string {
        if(!$value instanceof Entity) {
            $value = $this->inspector->inspect($value);
        }



        // Null
        if($value instanceof NativeNull) {
            return $this->renderNull($classes);
        }

        // Bool
        if($value instanceof NativeBoolean) {
            return $this->renderBoolean($value, $classes);
        }

        // Integer
        if($value instanceof NativeInteger) {
            return $this->renderInteger($value, $classes);
        }

        // Float
        if($value instanceof NativeFloat) {
            return $this->renderFloat($value, $classes);
        }

        // Binary
        if($value instanceof Binary) {
            return $this->renderBinary($value, $classes);
        }

        // Class string
        if($value instanceof ClassString) {
            return $this->renderClassString($value, $classes);
        }

        // String
        if($value instanceof NativeString) {
            return $this->renderString($value, $classes);
        }

        // Resource
        if($value instanceof NativeResource) {
            return $this->renderResource($value, $classes);
        }

        // Const option
        if($value instanceof ConstOption) {
            return $this->renderConstOption($value, $classes);
        }

        // Flag set
        if($value instanceof FlagSet) {
            return $this->renderFlagSet($value, $classes);
        }

        // Array
        if($value instanceof NativeArray) {
            return $this->renderArray($value, $level, $classes);
        }

        // Object
        if($value instanceof NativeObject) {
            return $this->renderObject($value, $level, $classes);
        }

        throw Exceptional::UnexpectedValue(
            'Invalid value type for rendering: ' . get_debug_type($value)
        );
    }

    public function renderNull(
        ?ClassList $classes = null
    ): string {
        return 'null';
    }

    public function renderBoolean(
        NativeBoolean $entity,
        ?ClassList $classes = null
    ): string {
        return $entity->value ? 'true' : 'false';
    }

    public function renderInteger(
        NativeInteger $entity,
        ?ClassList $classes = null
    ): string {
        return (string)$entity->value;
    }

    public function renderFloat(
        NativeFloat $entity,
        ?ClassList $classes = null
    ): string {
        return $this->normalizeFloat($entity->value);
    }

    protected function normalizeFloat(
        float $number
    ): string {
        $output = (string)$number;

        if (false === strpos($output, '.')) {
            $output .= '.0';
        }

        return $output;
    }

    public function renderIdentifier(
        string $identifier,
        ?ClassList $classes = null,
        ?int $singleLineMax = null
    ): string {
        return $identifier;
    }

    public function renderString(
        NativeString $entity,
        ?ClassList $classes = null,
        ?int $singleLineMax = null
    ): string {
        $isMultiLine =
            $singleLineMax === null &&
            false !== strpos($entity->value, "\n");

        if ($isMultiLine) {
            return $this->renderMultiLineString($entity, $classes);
        } else {
            return $this->renderSingleLineString($entity, $classes, $singleLineMax);
        }
    }

    public function renderSingleLineString(
        NativeString $entity,
        ?ClassList $classes = null,
        ?int $singleLineMax = null
    ): string {
        return $entity->value;
    }

    public function renderMultiLineString(
        NativeString $entity,
        ?ClassList $classes = null,
    ): string {
        return $entity->value;
    }


    public function renderStringLine(
        string $line,
        ?int $maxLength = null
    ): string {
        $shorten = false;

        if (
            $maxLength !== null &&
            strlen($line) > $maxLength
        ) {
            $shorten = true;
            $line = mb_substr($line, 0, $maxLength);
        }

        $output = $this->escape($line);

        $output = preg_replace_callback('/[[:cntrl:]]/u', function ($matches) {
            if (false === ($packed = unpack("H*", $matches[0]))) {
                throw Exceptional::UnexpectedValue('Unable to unpack control characters');
            }

            $hex = implode($packed);
            $output = $this->normalizeHexString($hex);
            return $this->renderControlCharacter($output);
        }, $output) ?? $output;

        if ($shorten) {
            $output .= $this->renderGrammar('…'); // @ignore-non-ascii
        }

        return $output;
    }

    protected function normalizeHexString(
        string $hex
    ): string {
        return match($hex) {
            '07' => '\\a',
            '1b' => '\\e',
            '0c' => '\\f',
            '0a' => '\\n',
            '0d' => '\\r',
            '09' => '\\t',
            default => '\\x' . $hex,
        };
    }

    public function renderBinary(
        Binary $entity,
        ?ClassList $classes = null,
    ): string {
        $output = [];
        $rows = $entity->splitChunkRows();

        foreach($rows as $row) {
            $line = [];

            foreach ($row as $chunk) {
                $line[] = $chunk;
            }

            $output[] = implode(' ', $line);
        }

        return implode("\n", $output);
    }

    public function renderClassString(
        ClassString $entity,
        ?ClassList $classes = null,
    ): string {
        $content = [];
        $content[] = $this->renderIdentifier($entity->type);
        $content[] = $this->renderGrammar('~');
        $content[] = $this->renderClassName($entity->value);

        return implode(' ', $content);
    }

    public function renderClassName(
        string $class,
        ?ClassList $classes = null,
    ): string {
        return $this->renderSignatureFqn($class, $classes);
    }

    public function renderControlCharacter(
        string $control
    ): string {
        return $control;
    }


    public function renderResource(
        NativeResource $entity,
        ?ClassList $classes = null,
    ): string {
        $container = new Container(
            type: 'resource',
            id: 'resource-'.$entity->id,
            objectId: $entity->id
        );

        $name = [];
        $name[] = $this->renderIdentifier('resource');
        $name[] = $this->renderGrammar('~');
        $name[] = $this->renderIdentifier($entity->type, ClassList::of('definition'));
        $container->renderedName = implode(' ', $name);

        $values = $entity->getMetaValues() ?? [];

        if(!empty($values)) {
            $meta = new Section(
                key: 'meta',
                open: false
            );

            $meta->renderedContent = $this->renderList(
                items: $values,
                style: ListStyle::Meta,
                includeKeys: true,
                classes: ClassList::of('meta')
            );

            $container->addSection($meta);
        }

        return $this->renderContainer($container);
    }




    public function renderConstOption(
        ConstOption $entity,
        ?ClassList $classes = null,
    ): string {
        $output = [];

        $name = $entity->getSelectedConstName();

        if($name !== null) {
            $output[] = $this->renderConstName(
                $name,
                ClassList::of('const-option')
            );

            $output[] = $this->renderGrammar('~');
        }

        $output[] = $this->renderValue(
            $entity->value,
            classes: ClassList::of('const-option-value')
        );

        return implode(' ', $output);
    }

    public function renderFlagSet(
        FlagSet $entity,
        ?ClassList $classes = null,
    ): string {
        $container = new Container(
            type: 'flagset',
            id: uniqid('flagset-'),
        );

        $name = [];
        $name[] = $this->renderIdentifier('flagset');
        $name[] = $this->renderGrammar('~');
        $name[] = $this->renderValue(
            $entity->value,
            classes: ClassList::of('flagset-value')
        );
        $container->renderedName = implode(' ', $name);


        $values = new Section('values', open: true);
        $items = [];

        foreach($entity->getSelectedConstValues() as $name => $value) {
            $items[] = new ConstOption(
                value: $value,
                constNames: [$name]
            );
        }

        $values->renderedContent = $this->renderList(
            items: $items,
            style: ListStyle::Values,
            includeKeys: false,
            classes: ClassList::of('flagset-values')
        );

        $container->addSection($values);
        return $this->renderContainer($container);
    }



    public function renderArray(
        NativeArray $entity,
        int $level = 0,
        ?ClassList $classes = null,
    ): string {
        $container = new Container(
            type: 'array',
            id: 'array-'.$entity->hash,
            open: $entity->open && $level < 3
        );

        $name = [];
        $name[] = $this->renderIdentifier(
            identifier: 'array',
            classes: ClassList::of('keyword')
        );
        $name[] = $this->renderGrammar(':');
        $name[] = $this->renderValue(
            value: $entity->length,
            classes: ClassList::of('length')
        );
        $container->renderedName = implode(' ', $name);

        if($entity->length) {
            $valueSection = new Section(
                key: 'values',
                open: true
            );
            $valueSection->renderedContent = $this->renderList(
                items: $entity->getPreparedValue(),
                style: ListStyle::Values,
                includeKeys: true,
                classes: ClassList::of('array-values'),
                level: $level + 1
            );
            $container->addSection($valueSection);
        }

        return $this->renderContainer($container);
    }


    public function renderObject(
        NativeObject $entity,
        int $level = 0,
        ?ClassList $classes = null,
    ): string {
        if($entity->referenced) {
            return $this->renderObjectReference($entity, $classes);
        }

        $container = new Container(
            type: 'object',
            id: $entity->id,
            objectId: $entity->objectId,
            open: $entity->open && $level < 3,
            sensitive: $entity->sensitive
        );


        $name = [];

        if($entity->lazy) {
            $name[] = $this->renderPointer(
                $entity->lazy->value
            );
        }

        $name[] = $this->renderClassName($entity->displayName);

        if($entity->itemName !== null) {
            $name[] = $this->renderGrammar('~');
            $name[] = $this->renderIdentifier(
                identifier: $entity->itemName,
                classes: ClassList::of('item-name')
            );
        }

        if($entity->length !== null) {
            $name[] = $this->renderGrammar(':');
            $name[] = $this->renderValue(
                value: $entity->length,
                classes: ClassList::of('length')
            );
        }

        $container->renderedName = implode(' ', $name);


        // Definition
        if (
            $entity->sections->isEnabled('definition') &&
            $entity->definition !== null
        ) {
            $definitionSection = new Section('definition');
            $definitionSection->renderedContent = $this->renderValue(
                value: trim($entity->definition),
                classes: ClassList::of('definition'),
            );

            $container->addSection($definitionSection);
        }


        // Text
        if (
            $entity->sections->isEnabled('text') &&
            $entity->text !== null
        ) {
            $textSection = new Section('text');

            if($entity instanceof ThrowableEntity) {
                $content = [];

                $content[] = $this->renderMultiLineString(
                    entity: new NativeString($entity->text),
                    classes: ClassList::of('exception')
                );

                if($file = $entity->file) {
                    $content[] = $this->renderStackFrameLocation(
                        file: $this->prettifyPath($file),
                        line: $entity->startLine,
                    );
                }

                $textSection->renderedContent = implode("\n", $content);
            } else {
                $textSection->renderedContent = $this->renderValue(
                    value: trim($entity->text),
                    classes: ClassList::of('text'),
                );
            }

            $container->addSection($textSection);
        }


        // Values
        if(
            $entity->sections->isEnabled('values') &&
            !empty($entity->values)
        ) {
            $valuesSection = new Section('values');
            $valuesSection->renderedContent = $this->renderList(
                items: $entity->values,
                style: ListStyle::Values,
                includeKeys: $entity->valueKeys,
                classes: ClassList::of('object-values'),
                level: $level + 1
            );
            $container->addSection($valuesSection);
        }


        // Properties
        if(
            $entity->sections->isEnabled('properties') &&
            !empty($entity->properties)
        ) {
            $propsSection = new Section('properties');
            $propsSection->renderedContent = $this->renderList(
                items: $entity->properties,
                style: ListStyle::Props,
                includeKeys: true,
                classes: ClassList::of('object-properties'),
                level: $level + 1
            );
            $container->addSection($propsSection);
        }



        // Stack
        if(
            $entity instanceof Traceable &&
            $entity->sections->isEnabled('stack') &&
            $entity->stackTrace !== null
        ) {
            $stackSection = new Section('stack');
            $stackSection->renderedContent = $this->renderStackTrace(
                trace: $entity->stackTrace,
                classes: ClassList::of('object-stack')
            );

            if($entity instanceof TraceEntity) {
                $stackSection->priority = -5;
            }

            $container->addSection($stackSection);
        }



        // Meta
        if(
            $entity->sections->isEnabled('meta') &&
            !empty($entity->meta)
        ) {
            $metaSection = new Section('meta', open: false);
            $metaSection->renderedContent = $this->renderList(
                items: $entity->meta,
                style: ListStyle::Meta,
                includeKeys: true,
                classes: ClassList::of('object-meta')
            );
            $container->addSection($metaSection);
        }



        // Info
        if($entity->sections->isEnabled('info')) {
            $infoSection = new Section('info', open: false);
            $infoSection->renderedContent = $this->renderList(
                items: $entity->getInfoValues(),
                style: ListStyle::Info,
                includeKeys: true,
                classes: ClassList::of('object-info')
            );
            $container->addSection($infoSection);
        }

        return $this->renderContainer($container);
    }

    public function renderObjectReference(
        NativeObject $entity,
        ?ClassList $classes = null,
    ): string {
        $output = [];
        $output[] = $this->renderClassName($entity->displayName, $classes);
        $output[] = $this->renderGrammar('&'. $entity->objectId);
        return implode(' ', $output);
    }



    public function renderContainer(
        Container $container,
    ): string {
        $output = [];
        $output[] = $container->renderedName;

        $container->sortSections();
        $sections = $container->getOpenSections();

        if(
            !empty($sections) ||
            $container->sensitive
        ) {
            $output[] = ' ';
            $output[] = $this->renderGrammar('{');
        }

        if($container->sensitive) {
            $output[] = ' '.$this->renderIdentifier(
                identifier: 'sensitive',
                classes: ClassList::of('sensitive')
            );
        }

        if($container->objectId !== null) {
            $output[] = ' ' . $this->renderGrammar(
                '#'.$container->objectId
            );
        }

        if(
            empty($sections) ||
            $container->sensitive
        ) {
            if($container->sensitive) {
                $output[] = ' '.$this->renderGrammar('}');
            }

            return implode('', $output);
        }

        $output[] = "\n";

        foreach ($sections as $section) {
            $output[] = $this->indent($section->renderedContent);
            $output[] = "\n";
        }

        $output[] = $this->renderGrammar('}');

        return implode('', $output);
    }



    #----------------------------- Stack Frames ----------------------------


    public function renderStackTrace(
        Trace $trace,
        ?ClassList $classes = null,
    ): string {
        $count = count($trace);
        $lines = [];

        foreach ($trace as $i => $frame) {
            $line = [];
            $line[] = $this->renderStackFrameNumber($count - $i);
            $line[] = $this->wrapSignature(
                $this->renderStackFrameSignature($frame)
            );
            $line[] = "\n   ";

            $line[] = $this->renderStackFrameLocation(
                file: $frame->callingFile,
                line: $frame->callingLine,
            );

            $lines[] = implode(' ', $line);
        }

        return $this->renderListStructure(
            lines: $lines,
            classes: ClassList::of('stack')
        );
    }


    public function renderStackFrameNumber(
        int $number,
        ?ClassList $classes = null,
    ): string {
        return str_pad((string)$number, 2);
    }

    public function renderStackFrameSignature(
        Frame $frame
    ): string {
        $output = [];

        // Namespace
        if (null !== ($class = $frame->class)) {
            $class = $frame::normalizeClassName($class);

            $output[] = $this->renderSignatureFqn($class);
        }

        // Type
        if ($frame->invokeType !== null) {
            $output[] = $this->renderGrammar($frame->invokeType);
        }

        // Function
        $function = $frame->function;

        if (
            $function === null ||
            str_contains($function, '{closure')
        ) {
            $output[] = $this->wrapSignatureFunction(
                $this->renderSignatureClosure(),
                ClassList::of('closure')
            );
        } else {
            if (str_contains($function, ',')) {
                $parts = explode(',', $function);
                $parts = array_map('trim', $parts);
                $function = [];
                $fArgs = [];

                $function[] = $this->renderGrammar('{');

                foreach ($parts as $part) {
                    $fArgs[] = $this->renderIdentifier($part);
                }

                $function[] = implode($this->renderGrammar(',') . ' ', $fArgs);
                $function[] = $this->renderGrammar('}');
                $function = implode($function);
            } else {
                $function = $this->escape($function);
            }

            $output[] = $this->wrapSignatureFunction($function);
        }

        // Args
        $output[] = $this->renderGrammar('(');
        $args = [];

        foreach ($frame->arguments as $arg) {
            if (is_object($arg)) {
                $args[] = $this->renderSignatureObject(
                    $frame::normalizeClassName(get_class($arg))
                );
            } elseif (is_array($arg)) {
                $args[] = $this->wrapSignatureArray(
                    $this->renderGrammar('[') . count($arg) . $this->renderGrammar(']')
                );
            } else if(is_string($arg)) {
                $args[] = $this->renderString(
                    new NativeString($arg),
                    singleLineMax: 16
                );
            } else if(is_resource($arg)) {
                $args[] = $this->renderSignatureResource($arg);
            } else {
                $args[] = $this->renderValue($arg);
            }
        }

        $output[] = implode($this->renderGrammar(', ') . ' ', $args);
        $output[] = $this->renderGrammar(')');

        return implode('', $output);
    }

    public function renderStackFrameLocation(
        ?string $file,
        ?int $line = null,
        ?ClassList $classes = null,
    ): string {
        $output = [];

        if ($file !== null) {
            $output[] = $this->renderStackFrameFile(
                $this->prettifyPath($file)
            );

            if ($line !== null) {
                $output[] = $this->renderStackFrameLine($line);
            }
        } else {
            $output[] = $this->renderStackFrameFile(
                'internal',
                ClassList::of('internal')
            );
        }

        return implode(' ', $output);
    }

    public function renderStackFrameFile(
        string $file,
        ?ClassList $classes = null,
    ): string {
        return $file;
    }

    public function renderStackFrameLine(
        int $line,
        ?ClassList $classes = null,
    ): string {
        return (string)$line;
    }

    public function renderStackFrameSource(
        Frame $frame
    ): ?string {
        return null;
    }



    #----------------------------- Signatures ----------------------------


    public function wrapSignature(
        string $signature,
        ?ClassList $classes = null,
    ): string {
        return $signature;
    }

    public function renderSignatureFqn(
        string $class,
        ?ClassList $classes = null,
    ): string {
        $output = [];

        if (str_starts_with($class, '~')) {
            $class = ltrim($class, '~');
            $output[] = $this->renderPointer('~');
        }

        $parts = explode('\\', $class);
        $class = array_pop($parts);

        if (!empty($parts)) {
            $parts[] = '';
        }

        foreach ($parts as $i => $part) {
            $parts[$i] = empty($part) ?
                null :
                $this->renderSignatureNamespace($part);
        }

        $output[] = implode($this->renderGrammar('\\'), $parts);
        $output[] = $this->renderSignatureClassName($class);

        return implode('', $output);
    }

    public function renderSignatureNamespace(
        string $namespace,
        ?ClassList $classes = null,
    ): string {
        return $namespace;
    }

    public function renderSignatureClassName(
        string $class,
        ?ClassList $classes = null,
    ): string {
        return $class;
    }

    public function renderSignatureConstant(
        string $constant,
        ?ClassList $classes = null,
    ): string {
        return $constant;
    }

    public function wrapSignatureFunction(
        string $function,
        ?ClassList $classes = null,
    ): string {
        return $function;
    }

    public function renderSignatureClosure(
        ?ClassList $classes = null,
    ): string {
        return 'closure';
    }

    public function wrapSignatureArray(
        string $array,
        ?ClassList $classes = null,
    ): string {
        return $array;
    }

    public function renderSignatureObject(
        string $object,
        ?ClassList $classes = null,
    ): string {
        return $object;
    }

    public function renderSignatureResource(
        $resource,
        ?ClassList $classes = null,
    ): string {
        return 'resource';
    }

    public function renderConstName(
        string $const,
        ?ClassList $classes = null,
    ): string {
        $parts = explode('::', $const, 2);
        $const = (string)array_pop($parts);

        if (empty($parts)) {
            $class = null;
            $parts = explode('\\', $const);
            $const = (string)array_pop($parts);
        } else {
            $parts = explode('\\', array_shift($parts));
            $class = array_pop($parts);
        }

        $namespace = implode('\\', $parts);

        $output = [];

        if (substr((string)$class, 0, 1) !== '~') {
            if(empty($namespace)) {
                $parts = [];
            } else {
                $parts = explode('\\', $namespace);
            }

            foreach ($parts as $i => $part) {
                $parts[$i] = empty($part) ? null : $this->renderSignatureNamespace($part);
            }

            $output[] = implode($this->renderGrammar('\\'), $parts);
        }

        if ($class !== null) {
            $output[] = $this->renderSignatureClassName($class);
            $output[] = $this->renderGrammar('::');
        }

        $output[] = $this->renderSignatureConstant($const);

        return $this->wrapSignature(
            implode('', $output),
            ClassList::of('const', $classes)
        );
    }



    #----------------------------- Lists ----------------------------



    public function renderList(
        array $items,
        string|ListStyle $style,
        bool $includeKeys = true,
        ?ClassList $classes = null,
        int $level = 0
    ): string {
        $style = ListStyle::fromAny($style);
        $lines = [];
        $pointer = '⇨'; // @ignore-non-ascii
        $asIdentifier = $access = false;

        switch ($style) {
            case ListStyle::Info:
            case ListStyle::Meta:
                $pointer = ':';
                $asIdentifier = true;
                break;

            case ListStyle::Props:
                $access = true;
                break;
        }

        foreach ($items as $key => $value) {
            $line = [];
            $key = (string)$key;

            if ($includeKeys) {
                $mods = [];

                if($value instanceof Property) {
                    $mods[] = $value->visibility->value;

                    if($value->virtual) {
                        $mods[] = 'virtual';
                    }

                    if ($value->readOnly) {
                        $mods[] = 'readonly';
                    }
                } elseif ($access) {
                    // Legacy style
                    $visibility = 'public';
                    $first = substr($key, 0, 1);

                    if ($first == '*') {
                        $key = substr($key, 1);
                        $visibility = 'protected';
                    } elseif ($first == '!') {
                        $key = substr($key, 1);
                        $visibility = 'private';
                    } elseif ($first == '%') {
                        $key = substr($key, 1);
                        $mods[] = 'virtual';
                    }

                    $mods[] = $visibility;
                }

                $mods[] = $style->value;

                $line[] = $this->renderIdentifier(
                    identifier: $key,
                    classes: ClassList::of('key', ...$mods),
                );

                $line[] = $this->renderPointer($pointer);
            }


            if($value instanceof Property) {
                $value = $value->value;
            }

            if (
                $asIdentifier &&
                is_array($value)
            ) {
                $isAssoc = !array_is_list($value);
                $line[] =
                    $this->renderGrammar('{') .
                    $this->renderList(
                        items: $value,
                        style: $style,
                        includeKeys: $isAssoc,
                        classes: ClassList::of(
                            $isAssoc ?
                                'map' :
                                'inline'
                        ),
                        level: $level + 1
                    ) .
                    $this->renderGrammar('}');
            } else if(
                is_string($value) &&
                $asIdentifier
            ) {
                $line[] = $this->renderIdentifier($value);
            } else {
                $line[] = $this->renderValue(
                    $value,
                    level: $level + 1,
                    classes: ClassList::of(
                        $asIdentifier ? 'identifier' : null
                    ),
                );
            }

            $lines[] = implode(' ', $line);
        }

        return $this->renderListStructure(
            lines: $lines,
            classes: ClassList::of('list', $style->value, $classes)
        );
    }

    public function renderListStructure(
        array $lines,
        ?ClassList $classes = null,
    ): string {
        $sep = $classes?->has('inline') ?
            ', ' :
            "\n";

        return implode($sep, $lines);
    }



    #----------------------------- Utils ----------------------------


    public function indent(
        string $lines
    ): string {
        if ($spaces = static::Spaces) {
            $space = str_repeat(' ', $spaces);
            $lines = $space . str_replace("\n", "\n" . $space, $lines);
        }

        return $lines;
    }

    public function escape(
        ?string $string
    ): string {
        return $string ?? '';
    }

    protected function prettifyPath(
        string $path
    ): string {
        if(class_exists(Monarch::class)) {
            return Monarch::$paths->prettify($path);
        }

        return $path;
    }

}
