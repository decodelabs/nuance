<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Renderer\Cli;

use BackedEnum;

trait FormatterTrait
{
    /**
     * @var array<array<string|Foreground|Background|Option|null>>
     */
    protected array $formatStack = [];

    /**
     * @param string|Option|array<string|Option> ...$options
     */
    protected function format(
        string $content,
        string|Foreground|null $foreground,
        string|Background|null $background = null,
        string|Option|array ...$options
    ): string {
        $options = $this->flattenOptions($options);
        $output = $this->setFormat($foreground, $background, ...$options);
        $output .= $content;

        $args = [$foreground, $background, ...$options];
        $output .= $this->applyStackedFormat($args);

        return $output;
    }

    /**
     * @param string|Option|array<string|Option> ...$options
     */
    protected function stackFormat(
        string|Foreground|null $foreground,
        string|Background|null $background = null,
        string|Option|array ...$options
    ): string {
        $options = $this->flattenOptions($options);

        array_unshift($this->formatStack, [
            $foreground,
            $background,
            ...$options
        ]);

        return $this->setFormat($foreground, $background, ...$options);
    }

    /**
     * @param string|Option|array<string|Option> ...$options
     */
    protected function setFormat(
        string|Foreground|null $foreground,
        string|Background|null $background = null,
        string|Option|array ...$options
    ): string {
        $options = $this->flattenOptions($options);
        $setCodes = [];

        if ($foreground !== null) {
            $setCodes[] = Foreground::toValue($foreground);
        }

        if ($background !== null) {
            $setCodes[] = Background::toValue($background);
        }

        foreach ($options as $option) {
            $setCodes[] = Option::toValue($option);
        }

        return sprintf("\033[%sm", implode(';', $setCodes));
    }

    /**
     * @param string|Option|array<string|Option> ...$options
     */
    protected function resetFormat(
        string|Foreground|null $foreground,
        string|Background|null $background = null,
        string|Option|array ...$options
    ): string {
        $options = $this->flattenOptions($options);
        $setCodes = [];
        $setCodes[] = Foreground::Reset->value;
        $setCodes[] = Background::Reset->value;

        foreach ($options as $option) {
            $setCodes[] = Option::toReset($option);
        }

        return sprintf("\033[%sm", implode(';', $setCodes));
    }

    protected function popFormat(): string
    {
        $args = array_shift($this->formatStack);

        if ($args === null) {
            return '';
        }

        return $this->applyStackedFormat($args);
    }

    /**
     * @param array<string|BackedEnum|null> $args
     */
    protected function applyStackedFormat(
        array $args
    ): string {
        $output = $this->resetFormat(...$args);

        if (isset($this->formatStack[0])) {
            $args = $this->formatStack[0];

            if (!isset($args[0])) {
                $args[0] = 'reset';
            }

            if (!isset($args[1])) {
                $args[1] = 'reset';
            }

            // @phpstan-ignore-next-line
            $output .= $this->setFormat(...$args);
        }

        return $output;
    }

    /**
     * @param array<string|Option|array<string|Option>> $options
     * @return array<Option>
     */
    protected function flattenOptions(
        array $options
    ): array {
        $output = [];

        foreach ($options as $option) {
            if (is_array($option)) {
                $output = array_merge($output, $this->flattenOptions($option));
            } elseif ($option instanceof Option) {
                $output[] = $option;
            } else {
                $output[] = Option::fromAny($option);
            }
        }

        return $output;
    }
}
