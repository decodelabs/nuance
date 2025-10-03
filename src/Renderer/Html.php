<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Renderer;

use DecodeLabs\Coercion;
use DecodeLabs\Nuance\Entity\Binary;
use DecodeLabs\Nuance\Entity\ClassString;
use DecodeLabs\Nuance\Entity\ConstOption;
use DecodeLabs\Nuance\Entity\NativeBoolean;
use DecodeLabs\Nuance\Entity\NativeFloat;
use DecodeLabs\Nuance\Entity\NativeInteger;
use DecodeLabs\Nuance\Entity\NativeObject;
use DecodeLabs\Nuance\Entity\NativeString;
use DecodeLabs\Nuance\Renderer;
use DecodeLabs\Nuance\RendererTrait;
use DecodeLabs\Nuance\Structure\ClassList;
use DecodeLabs\Nuance\Structure\Container;
use DecodeLabs\Remnant\ArgumentFormat;
use DecodeLabs\Remnant\ClassIdentifier\Native as NativeClass;
use DecodeLabs\Remnant\Filter\Vendor as VendorFilter;
use DecodeLabs\Remnant\Location;
use DecodeLabs\Remnant\Trace;
use DecodeLabs\Remnant\ViewOptions;

class Html implements Renderer
{
    use RendererTrait;

    public const int Spaces = 0;


    #----------------------------- Grammar ----------------------------


    public function renderGrammar(
        string $grammar
    ): string {
        return $this->el(
            tag: 'span',
            content: $this->escape($grammar),
            classes: ClassList::of('g')
        );
    }

    public function renderPointer(
        string $pointer
    ): string {
        return $this->el(
            tag: 'span',
            content: $this->escape($pointer),
            classes: ClassList::of('pointer')
        );
    }



    #----------------------------- Types ----------------------------


    public function renderNull(
        ?ClassList $classes = null
    ): string {
        return $this->el(
            tag: 'span',
            content: 'null',
            classes: ClassList::of('null', $classes)
        );
    }

    public function renderBoolean(
        NativeBoolean $entity,
        ?ClassList $classes = null
    ): string {
        return $this->el(
            tag: 'span',
            content: $entity->value ? 'true' : 'false',
            classes: ClassList::of('bool', $classes)
        );
    }

    public function renderInteger(
        NativeInteger $entity,
        ?ClassList $classes = null
    ): string {
        return $this->el(
            tag: 'span',
            content: (string)$entity->value,
            classes: ClassList::of('int', $classes)
        );
    }

    public function renderFloat(
        NativeFloat $entity,
        ?ClassList $classes = null
    ): string {
        return $this->el(
            tag: 'span',
            content: $this->normalizeFloat($entity->value),
            classes: ClassList::of('float', $classes)
        );
    }


    public function renderIdentifier(
        string $identifier,
        ?ClassList $classes = null,
        ?int $singleLineMax = null
    ): string {
        return $this->el(
            tag: 'span',
            content: $this->renderStringLine($identifier, $singleLineMax),
            classes: ClassList::of('identifier', $classes)
        );
    }

    public function renderSingleLineString(
        NativeString $entity,
        ?ClassList $classes = null,
        ?int $singleLineMax = null
    ): string {
        $content = [];

        $content[] = $this->el(
            tag: 'span',
            content: $this->renderStringLine($entity->value, $singleLineMax),
            classes: ClassList::of('line')
        );

        if ($singleLineMax === null) {
            $content[] = $this->el(
                tag: 'span',
                content: (string)mb_strlen($entity->value),
                classes: ClassList::of('length')
            );
        }

        return $this->el(
            tag: 'span',
            content: implode('', $content),
            classes: ClassList::of('string', 's', $classes)
        );
    }

    public function renderMultiLineString(
        NativeString $entity,
        ?ClassList $classes = null,
    ): string {
        $content = [];
        $string = str_replace("\r", '', $entity->value);
        $parts = explode("\n", $string);
        $count = count($parts);
        $large = $count > 10;
        $el = 'div';

        if ($large) {
            $el = 'label';
            $content[] = $this->el(
                tag: 'input',
                attributes: [
                    'type' => 'checkbox',
                ]
            );
        }

        $content[] = $this->el(
            tag: 'span',
            content: (string)mb_strlen($string),
            classes: ClassList::of('length')
        );

        $lines = [];

        foreach ($parts as $part) {
            $lines[] = $this->el(
                tag: 'div',
                content: $this->renderStringLine($part),
                classes: ClassList::of('line')
            );
        }

        $content[] = $this->el(
            tag: 'div',
            content: implode('', $lines),
            classes: ClassList::of('lines')
        );

        return $this->el(
            tag: $el,
            content: implode('', $content),
            classes: ClassList::of('string', 'm', $large ? 'large' : null, $classes),
        );
    }


    public function renderBinary(
        Binary $entity,
        ?ClassList $classes = null,
    ): string {
        $output = [];
        $el = 'div';

        $output[] = $this->el(
            tag: 'span',
            content: (string)strlen($entity->value),
            classes: ClassList::of('length')
        );


        $rows = $entity->splitChunkRows();
        $count = count($rows);
        $large = $count > 4;

        if ($large) {
            $el = 'label';
            $output[] = $this->el(
                tag: 'input',
                attributes: [
                    'type' => 'checkbox',
                ]
            );
        }

        $lines = [];

        foreach ($rows as $row) {
            $line = [];

            foreach ($row as $chunk) {
                $line[] = $this->el(
                    tag: 'i',
                    content: $chunk
                );
            }

            $lines[] = $this->el(
                tag: 'div',
                content: implode(' ', $line),
                classes: ClassList::of('line', 'b')
            );
        }

        $output[] = $this->el(
            tag: 'div',
            content: implode('', $lines),
            classes: ClassList::of('lines')
        );

        return $this->el(
            tag: $el,
            content: implode('', $output),
            classes: ClassList::of('string', 'b', $large ? 'large' : null, $classes)
        );
    }

    public function wrapBinaryString(
        string $string,
        int $length,
        ?ClassList $classes = null,
    ): string {
        $content = [];

        $content[] = $string;

        $content[] = $this->el(
            tag: 'span',
            content: (string)$length,
            classes: ClassList::of('length')
        );

        return $this->el(
            tag: 'div',
            content: implode('', $content),
            classes: ClassList::of('string', 'b', $classes)
        );
    }

    public function renderClassString(
        ClassString $entity,
        ?ClassList $classes = null,
    ): string {
        $content = [];
        $content[] = $this->renderIdentifier($entity->type);
        $content[] = $this->renderGrammar('~');
        $content[] = $this->renderClassName($entity->value);

        return $this->el(
            tag: 'span',
            content: implode(' ', $content),
            classes: ClassList::of('class-string', $classes)
        );
    }

    public function renderClassName(
        string $class,
        ?ClassList $classes = null,
    ): string {
        /** @var class-string $class */
        return $this->el(
            tag: 'span',
            content: $this->renderSignatureFqn(new NativeClass($class)),
            classes: ClassList::of('signature', 'source', $classes)
        );
    }

    public function renderControlCharacter(
        string $control
    ): string {
        $classes = ClassList::of('control');

        if ($control === '\\t') {
            $classes->add('tab');
        }

        return $this->el(
            tag: 'span',
            content: $control,
            classes: $classes
        );
    }



    public function renderConstOption(
        ConstOption $entity,
        ?ClassList $classes = null,
    ): string {
        $output = [];

        $name = $entity->getSelectedConstName();

        if ($name !== null) {
            $output[] = $this->renderConstName($name);
        }

        if (is_array($entity->value)) {
            $output[] = $this->renderIdentifier(
                'array',
                ClassList::of('keyword')
            );
        } else {
            $output[] = $this->renderGrammar('~');

            $classes = ClassList::of('value');

            if (is_string($entity->value)) {
                $output[] = $this->renderString(
                    new NativeString($entity->value),
                    classes: $classes,
                    singleLineMax: 32
                );
            } else {
                $output[] = $this->renderValue(
                    $entity->value,
                    classes: $classes
                );
            }
        }

        return $this->el(
            tag: 'span',
            content: implode(' ', $output),
            classes: ClassList::of('const-option', $classes)
        );
    }



    public function renderObjectReference(
        NativeObject $entity,
        ?ClassList $classes = null,
    ): string {
        $output = [];
        $output[] = $this->renderClassName($entity->displayName, $classes);
        $output[] = $this->renderIdentifier(
            (string)$entity->objectId,
            ClassList::of('object-ref')
        );

        return $this->el(
            tag: 'a',
            content: implode(' ', $output),
            classes: ClassList::of('object-reference', $classes),
            attributes: [
                'href' => '#' . $entity->id,
            ]
        );
    }




    public function renderContainer(
        Container $container,
    ): string {
        $output = $header = $buttons = [];

        $container->sortSections();

        $header[] = $this->el(
            tag: 'label',
            content: $container->renderedName,
            classes: ClassList::of('name'),
            attributes: [
                'for' => $container->id . '-toggle',
            ]
        );

        $openId = $container->getOpenId();
        $hasOpenSection = false;
        $hasBrackets = !$container->sensitive && !empty($container->sections);

        foreach ($container->sections as $section) {
            if ($section->open) {
                $hasOpenSection = true;
            }

            if (!$container->sensitive) {
                $isOpen = $section->id === $openId;
                $button = [];
                $button[] = $this->el(
                    tag: 'input',
                    attributes: [
                        'type' => 'radio',
                        'name' => $container->id . '-toggle',
                        'value' => $section->id,
                        'checked' => $isOpen,
                        'class' => 'section-toggle',
                    ]
                );
                $button[] = '<i>' . $section->keyChar . '</i>';

                $buttons[] = $this->el(
                    tag: 'label',
                    content: implode('', $button),
                    classes: ClassList::of('badge', $section->key, $isOpen ? 'open' : null),
                );
            }
        }

        if (count($buttons) > 1) {
            $header[] = $this->el(
                tag: 'div',
                content: implode('', $buttons),
                classes: ClassList::of('buttons')
            );
        }

        if ($hasBrackets) {
            $header[] = $this->renderGrammar('{');
        }

        if ($container->objectId !== null) {
            $header[] = $this->renderIdentifier(
                (string)$container->objectId,
                ClassList::of('object-id')
            );
        }

        if ($container->sensitive) {
            if ($hasBrackets) {
                $header[] = $this->renderGrammar('}');
            }
        } else {
            $header[] = $this->el(
                tag: 'input',
                attributes: [
                    'type' => 'checkbox',
                    'id' => $container->id . '-toggle',
                    'checked' => $container->open && $openId && $hasOpenSection,
                    'class' => 'container-toggle',
                ]
            );
        }

        $output[] = $this->el(
            tag: 'header',
            content: implode(' ', $header),
        );


        if (
            !empty($container->sections) &&
            !$container->sensitive
        ) {
            $body = [];

            foreach ($container->sections as $section) {
                $isOpen = $section->id === $openId;
                $sectionContent = [];

                $sectionContent[] = $this->el(
                    tag: 'input',
                    attributes: [
                        'type' => 'radio',
                        'name' => $container->id . '-section',
                        'value' => $section->id,
                        'id' => $section->id,
                        'checked' => $isOpen,
                    ]
                );

                $sectionContent[] = $section->renderedContent;

                $body[] = $this->el(
                    tag: 'div',
                    content: implode('', $sectionContent),
                    classes: ClassList::of('section', $section->key),
                );
            }

            $output[] = $this->el(
                tag: 'div',
                content: implode('', $body),
                classes: ClassList::of('body')
            );

            $output[] = $this->renderGrammar('}');
        }

        return $this->el(
            tag: 'div',
            content: implode('', $output),
            classes: ClassList::of('container', $container->type),
            attributes: [
                'id' => $container->id,
            ]
        );
    }



    #----------------------------- Stack Frames ----------------------------


    public function renderStackTrace(
        Trace $trace,
        ?ClassList $classes = null,
    ): string {
        $output = [];
        $count = count($trace);
        $lines = [];
        $first = true;

        $options = $trace->options ?? new ViewOptions(
            argumentFormat: ArgumentFormat::NamedValues,
            collapseSingleLineArguments: true,
            filters: [
                new VendorFilter(),
            ]
        );

        foreach ($trace as $i => $frame) {
            $filtered =
                $i > 0 &&
                !$options->filter($frame);

            $id = uniqid('frame-');
            $line = $sig = [];

            $line[] = $this->el(
                tag: 'input',
                content: null,
                attributes: [
                    'type' => 'checkbox',
                    'id' => $id,
                    'checked' => $first
                ]
            );

            $line[] = '<samp class="dump trace">';

            $sig[] = $this->renderStackFrameNumber($count - $i);
            $sig[] = $this->wrapSignature($this->renderStackFrameSignature($frame, $options));
            $sig[] = "\n   ";

            $sig[] = $this->renderStackFrameLocation(
                $frame->callSite ?? $frame->location
            );

            $line[] = implode(' ', $sig);
            $line[] = '</samp>';

            if (null !== ($source = $this->renderStackFrameSource($frame))) {
                $line[] = $source;
            }

            $lines[] = $this->el(
                tag: 'label',
                content: implode("\n", $line),
                classes: ClassList::of('stack-frame', 'group', $filtered ? 'filtered' : null)
            );

            $first = false;
        }

        $output[] = $this->renderListStructure(
            lines: $lines,
            classes: ClassList::of('stack')
        );

        return implode("\n", $output);
    }

    public function wrapStackFrameArgument(
        string $argument,
        ?ClassList $classes = null,
    ): string {
        return $this->el(
            tag: 'div',
            content: $argument,
            classes: ClassList::of('argument', $classes)
        );
    }

    public function renderStackFrameNumber(
        int $number,
        ?ClassList $classes = null,
    ): string {
        return $this->el(
            tag: 'span',
            content: (string)$number,
            classes: ClassList::of('number')
        );
    }

    public function renderStackFrameLocation(
        ?Location $location,
        ?ClassList $classes = null,
    ): string {
        $output = [];

        if ($location !== null) {
            $file = $location->getPrettyFile();
            $output[] = $this->renderStackFrameFile($file);

            if ($location->line !== null) {
                $output[] = $this->renderGrammar(':');
                $output[] = $this->renderStackFrameLine($location->line);
            }

            if ($location->evalLine !== null) {
                $output[] = ' ';
                $output[] = $this->renderGrammar('[');
                $output[] = $this->renderIdentifier('eval');
                $output[] = $this->renderGrammar(':');
                $output[] = $this->renderStackFrameLine($location->evalLine);
                $output[] = $this->renderGrammar(']');
            }
        } else {
            $output[] = $this->renderStackFrameFile(
                'internal',
                ClassList::of('internal')
            );
        }

        return $this->el(
            tag: 'span',
            content: implode('', $output),
            classes: ClassList::of('location', $classes)
        );
    }

    public function renderStackFrameFile(
        string $file,
        ?ClassList $classes = null,
    ): string {
        $content = '';

        if (preg_match('/^(@[a-z]+):(.+)$/', $file, $matches)) {
            $content = $this->el(
                tag: 'span',
                content: $matches[1],
                classes: ClassList::of('identifier', 'definition-key', 'alias')
            );

            $content .= $this->renderGrammar(':');
            $file = $matches[2];
        }

        $content .= $this->escape($file);

        return $this->el(
            tag: 'span',
            content: $content,
            classes: ClassList::of('file', $classes)
        );
    }

    public function renderStackFrameLine(
        int $line,
        ?ClassList $classes = null,
    ): string {
        return $this->el(
            tag: 'span',
            content: (string)$line,
            classes: ClassList::of('line', $classes)
        );
    }


    #----------------------------- Signatures ----------------------------


    public function wrapSignature(
        string $signature,
        ?ClassList $classes = null,
    ): string {
        return $this->el(
            tag: 'span',
            content: $signature,
            classes: ClassList::of('signature', 'source', $classes)
        );
    }

    public function wrapSignatureNamespace(
        string $namespace,
        ?ClassList $classes = null,
        ?string $fqn = null,
    ): string {
        return $this->el(
            tag: 'span',
            content: $this->escape($namespace),
            classes: ClassList::of('namespace', $classes),
            attributes: [
                'title' => $fqn,
            ]
        );
    }

    public function wrapSignatureClassName(
        string $class,
        ?ClassList $classes = null,
        ?string $fqn = null,
    ): string {
        return $this->el(
            tag: 'span',
            content: $class,
            classes: ClassList::of('class', $classes),
            attributes: [
                'title' => $fqn,
            ]
        );
    }

    public function renderSignatureConstant(
        string $constant,
        ?ClassList $classes = null,
    ): string {
        return $this->el(
            tag: 'span',
            content: $this->escape($constant),
            classes: ClassList::of('constant', $classes)
        );
    }

    public function wrapSignatureFunction(
        string $function,
        ?ClassList $classes = null,
    ): string {
        return $this->el(
            tag: 'span',
            content: $function,
            classes: ClassList::of('function', $classes)
        );
    }

    public function wrapSignatureArray(
        string $array,
        ?ClassList $classes = null,
    ): string {
        return $this->el(
            tag: 'span',
            content: $array,
            classes: ClassList::of('array', $classes)
        );
    }

    public function renderSignatureObject(
        string $object,
        ?ClassList $classes = null,
    ): string {
        return $this->el(
            tag: 'span',
            content: $this->escape($object),
            classes: ClassList::of('class', 'param', $classes)
        );
    }


    public function renderSignatureResource(
        $resource,
        ?ClassList $classes = null,
    ): string {
        return $this->el(
            tag: 'span',
            content: 'resource',
            classes: ClassList::of('resource', $classes),
        );
    }



    #----------------------------- Lists ----------------------------


    public function renderListStructure(
        array $lines,
        ?ClassList $classes = null,
    ): string {
        return "\n" .
            $this->el(
                tag: 'ul',
                content: implode("\n", array_map(
                    fn ($line) => $this->el('li', $line),
                    $lines
                )),
                classes: $classes
            ) .
            "\n";
    }



    #----------------------------- Utils ----------------------------


    public function escape(
        ?string $string
    ): string {
        if ($string === null) {
            return '';
        }

        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }


    /**
     * @param array<string,mixed> $attributes
     */
    protected function el(
        string $tag,
        ?string $content = null,
        ?ClassList $classes = null,
        array $attributes = []
    ): string {
        if ($classes) {
            $classes = clone $classes;
        } else {
            $classes = new ClassList();
        }

        if (isset($attributes['class'])) {
            $classes->add(...explode(' ', Coercion::toString($attributes['class'])));
        }

        $attributes['class'] = (string)$classes;
        $attrStrings = [];

        foreach ($attributes as $name => $value) {
            if (
                $value === null ||
                $value === false
            ) {
                continue;
            }

            if (is_bool($value)) {
                $attrStrings[] = $name;
            } else {
                $attrStrings[] = $name . '="' . htmlspecialchars(Coercion::toString($value), ENT_QUOTES) . '"';
            }
        }

        $output = '<' . $tag
            . (!empty($attrStrings) ? ' ' . implode(' ', $attrStrings) : '')
            . '>';

        if ($tag === 'input') {
            return $output;
        }

        return
            $output .
            (string)$content .
            '</' . $tag . '>';
    }
}
