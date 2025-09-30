# Nuance

[![PHP from Packagist](https://img.shields.io/packagist/php-v/decodelabs/nuance?style=flat)](https://packagist.org/packages/decodelabs/nuance)
[![Latest Version](https://img.shields.io/packagist/v/decodelabs/nuance.svg?style=flat)](https://packagist.org/packages/decodelabs/nuance)
[![Total Downloads](https://img.shields.io/packagist/dt/decodelabs/nuance.svg?style=flat)](https://packagist.org/packages/decodelabs/nuance)
[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/decodelabs/nuance/integrate.yml?branch=develop)](https://github.com/decodelabs/nuance/actions/workflows/integrate.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-44CC11.svg?longCache=true&style=flat)](https://github.com/phpstan/phpstan)
[![License](https://img.shields.io/packagist/l/decodelabs/nuance?style=flat)](https://packagist.org/packages/decodelabs/nuance)

### Type inspection tools

Nuance provides a comprehensive suite of inspection and entity rendering tools to allow for deep data structure dumping functionality.

---

## Installation

This package requires PHP 8.4 or higher.

Install via Composer:

```bash
composer require decodelabs/nuance
```

## Usage

This library is intended to be integrated into larger debug tools - it is the heart of the inspection and rendering functionality, but does not provide a user interface by itself. See [Glitch](https://github.com/decodelabs/glitch) to use Nuance in a user-friendly way.

Load a `Renderer` and pass a value - Nuance will inspect it and any nested values and return a string representation of the value in the format denoted by the Renderer:

```php
use DecodeLabs\Nuance\Renderer\Html as HtmlRenderer;

$renderer = new HtmlRenderer();
$value = ['foo' => 'bar', 'baz' => ['qux' => 'quux']];
$output = $renderer->render($value);
```

The output from the HTML renderer is just the raw markup - it up to the implementing system to provide styles and scripts to make it presentable.

## Custom dumps

It is possible to define custom dump information for your userland objects, allowing for a more tailored representation of your data structures. This is done by implementing the `DecodeLabs\Nuance\Dumpable` interface on your classes.

Constructing an instanceof `DecodeLabs\Nuance\Entity\NativeObject` will inspect the basic properties of the object and you can then add optional information to extend the dump output.

```php
use DecodeLabs\Nuance\Dumpable;
use DecodeLabs\Nuance\Entity\NativeObject;

class MyCustomObject implements Dumpable
{
    public function toNuanceEntity(): NativeObject
    {
        $output = new NativeObject($this);

        // Custom display name - rendered as a class name
        $output->displayName = 'Custom\\Object';

        // Item name - an extension of the display name, useful for sub-types or options
        $output->itemName = 'option-1';

        // Sensitive - marks the entire object as sensitive, which will prevent
        // data within it from being displayed
        $output->sensitive = true;

        // Numerical size of the object, if applicable
        $output->length = 42;

        // Definition string
        $output->definition = '<xml>MyCustomObject</xml>';

        // Text string
        $output->text = 'My custom object text representation';

        // Intrinsic values of the object - useful for collection-like objects
        $output->values = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        // Don't show keys from values list
        $output->valueKeys = false;

        // Shortcut to setting a single value in value list without keys
        $output->value = 'single value';

        // Properties - bona fide members of the object, manually collected
        $output->setProperty(
            name: 'property1',
            value: 'value1',
            visibility: 'protected',
            virtual: false,
            readOnly: true
        );

        // Metadata - additional information about the object
        $output->meta = [
            'created_at' => '2023-10-01',
            'updated_at' => '2023-10-02',
        ];

        // Programatically disable dump sections
        $output->sections->disable('info');

        return $output;
    }
}
```


## Licensing

Nuance is licensed under the MIT License. See [LICENSE](./LICENSE) for the full license text.
