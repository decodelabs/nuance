<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Renderer;

use DecodeLabs\Nuance\Entity\Binary;
use DecodeLabs\Nuance\Entity\NativeBoolean;
use DecodeLabs\Nuance\Entity\NativeFloat;
use DecodeLabs\Nuance\Entity\NativeInteger;
use DecodeLabs\Nuance\Entity\NativeString;
use DecodeLabs\Nuance\Renderer;
use DecodeLabs\Nuance\Renderer\Cli\FormatterTrait;
use DecodeLabs\Nuance\RendererTrait;
use DecodeLabs\Nuance\Structure\ClassList;

class Cli implements Renderer
{
    use RendererTrait;
    use FormatterTrait;

    #----------------------------- Grammar ----------------------------

    public function renderGrammar(
        string $grammar
    ): string {
        return $this->format(
            content: $grammar,
            foreground: 'white',
            options: 'dim'
        );
    }

    public function renderPointer(
        string $pointer
    ): string {
        return $this->format(
            content: $pointer,
            foreground: 'white',
            options: 'dim'
        );
    }



    #----------------------------- Types ----------------------------


    public function renderNull(
        ?ClassList $classes = null
    ): string {
        return $this->format(
            content: 'null',
            foreground: 'magenta',
            options: 'bold'
        );
    }

    public function renderBoolean(
        NativeBoolean $entity,
        ?ClassList $classes = null
    ): string {
        return $this->format(
            content: $entity->value ? 'true' : 'false',
            foreground: 'magenta',
            options: 'bold'
        );
    }

    public function renderInteger(
        NativeInteger $entity,
        ?ClassList $classes = null
    ): string {
        return $this->format(
            content: (string)$entity->value,
            foreground: 'blue',
            options: 'bold'
        );
    }

    public function renderFloat(
        NativeFloat $entity,
        ?ClassList $classes = null
    ): string {
        return $this->format(
            content: $this->normalizeFloat($entity->value),
            foreground: 'blue',
            options: 'bold'
        );
    }


    public function renderIdentifier(
        string $identifier,
        ?ClassList $classes = null,
        ?int $singleLineMax = null
    ): string {
        $classes = ClassList::of($classes);
        $options = [];

        if ($classes->isEmpty()) {
            $classes->add('values', 'public');
        }

        $color = 'white';
        $output = '';

        if ($classes->has('info')) {
            $color = 'cyan';
        } elseif ($classes->has('meta')) {
            $color = 'white';
        } elseif ($classes->has('keyword')) {
            $color = 'green';
        } elseif ($classes->has('sensitive')) {
            $color = 'red';
            $options[] = 'bold';
        } elseif ($classes->has('props')) {
            $color = 'white';

            if ($classes->has('protected')) {
                $output .= $this->format(
                    content: '*',
                    foreground: 'blue',
                    options: 'bold'
                );
            } elseif ($classes->has('private')) {
                $output .= $this->format(
                    content: '!',
                    foreground: 'red',
                    options: 'bold'
                );
            }

            if ($classes->has('virtual')) {
                $output .= $this->format(
                    content: '%',
                    foreground: 'yellow',
                    options: 'bold'
                );
            }
        } elseif ($classes->has('values')) {
            $color = 'yellow';
        }

        $output .= $this->stackFormat(
            foreground: $color,
            options: $options
        );

        $output .= $this->renderStringLine($identifier, $singleLineMax);
        $output .= $this->popFormat();
        return $output;
    }

    public function renderSingleLineString(
        NativeString $entity,
        ?ClassList $classes = null,
        ?int $singleLineMax = null
    ): string {
        $output = $this->format(
            content: '"',
            foreground: 'white',
            options: 'dim'
        );

        $output .= $this->stackFormat(
            foreground: 'red',
            options: 'bold'
        );

        $output .= $this->renderStringLine($entity->value, $singleLineMax);
        $output .= $this->popFormat();

        $output .= $this->format(
            content: '"',
            foreground: 'white',
            options: 'dim'
        );

        return $output;
    }

    public function renderMultiLineString(
        NativeString $entity,
        ?ClassList $classes = null,
    ): string {
        $output = [];
        $string = str_replace("\r", '', $entity->value);
        $parts = explode("\n", $string);
        $quotes = $classes?->has('exception') ? '!!!' : '"""';

        $output[] = $this->format(
            content: $quotes . ' ' . mb_strlen($string),
            foreground: 'white',
            options: 'dim'
        );

        foreach ($parts as $part) {
            $output[] =
                $this->format(
                    content: $this->renderStringLine($part),
                    foreground: 'red',
                    options: 'bold'
                ) .
                $this->format(
                    content: '⏎', // @ignore-non-ascii
                    foreground: 'white',
                    options: 'dim'
                );
        }

        $output[] = $this->format(
            content: $quotes,
            foreground: 'white',
            options: 'dim'
        );

        return implode("\n", $output);
    }

    public function renderBinary(
        Binary $entity,
        ?ClassList $classes = null,
    ): string {
        $output = [];
        $rows = $entity->splitChunkRows();

        $output[] = $this->format(
            content: '@@@ ' . strlen($entity->value),
            foreground: 'white',
            options: 'dim'
        );

        foreach ($rows as $row) {
            $line = [];

            foreach ($row as $chunk) {
                $line[] = $this->format(
                    content: $chunk,
                    foreground: 'magenta'
                );
            }

            $output[] = implode(' ', $line);
        }

        $output[] = $this->format(
            content: '@@@',
            foreground: 'white',
            options: 'dim'
        );

        return implode("\n", $output);
    }

    public function renderControlCharacter(
        string $control
    ): string {
        return $this->format(
            content: $control,
            foreground: 'white',
            background: 'red',
            options: 'bold'
        );
    }



    #----------------------------- Stack Frames ----------------------------


    public function renderStackFrameNumber(
        int $number,
        ?ClassList $classes = null,
    ): string {
        return $this->format(
            content: str_pad((string)$number, 2),
            foreground: 'blue',
            options: 'bold'
        );
    }

    public function renderStackFrameFile(
        string $path,
        ?ClassList $classes = null,
    ): string {
        return $this->format(
            content: $path,
            foreground: 'yellow'
        );
    }

    public function renderStackFrameLine(
        int $number,
        ?ClassList $classes = null,
    ): string {
        return $this->format(
            content: (string)$number,
            foreground: 'magenta',
            options: 'bold'
        );
    }


    #----------------------------- Signatures ----------------------------


    public function renderSignatureNamespace(
        string $namespace,
        ?ClassList $classes = null,
    ): string {
        return $this->format(
            content: $namespace,
            foreground: 'cyan'
        );
    }

    public function renderSignatureClassName(
        string $class,
        ?ClassList $classes = null,
    ): string {
        return $this->format(
            content: $class,
            foreground: 'cyan',
            options: 'bold'
        );
    }

    public function renderSignatureConstant(
        string $constant,
        ?ClassList $classes = null,
    ): string {
        return $this->format(
            content: $constant,
            foreground: 'magenta'
        );
    }

    public function wrapSignatureFunction(
        string $function,
        ?ClassList $classes = null,
    ): string {
        $output = '';

        if ($classes?->has('closure')) {
            $output .= $this->renderGrammar('{');
        }

        $output .= $this->format(
            content: $function,
            foreground: 'blue'
        );

        if ($classes?->has('closure')) {
            $output .= $this->renderGrammar('}');
        }

        return $output;
    }

    public function renderSignatureObject(
        string $object,
        ?ClassList $classes = null,
    ): string {
        return $this->format(
            content: $object,
            foreground: 'green'
        );
    }


    #----------------------------- Lists ----------------------------


    public function renderListStructure(
        array $lines,
        ?ClassList $classes = null,
    ): string {
        $isInline = $classes?->has('inline') ?? false;
        $wrap = false;

        if ($isInline) {
            $wrap = true;
            $test = implode(', ', $lines);

            if (strlen($test) > 80) {
                $isInline = false;
            }
        }

        $sep = $isInline ?
            $this->format(
                content: ', ',
                foreground: 'white',
                options: 'dim'
            ) :
            "\n";

        $output = implode($sep, $lines);

        if ($wrap) {
            if ($isInline) {
                $output = ' ' . $output . ' ';
            } else {
                $output = $this->indent("\n" . $output) . "\n";
            }
        }

        return $output;
    }
}
