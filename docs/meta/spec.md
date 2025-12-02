# Nuance — Package Specification

> **Cluster:** `runtime`
> **Language:** `php`
> **Milestone:** `m1`
> **Repo:** `https://github.com/decodelabs/nuance`
> **Role:** Dump inspector

## Overview

### Purpose

Nuance provides a comprehensive suite of inspection and entity rendering tools for deep data structure dumping functionality. It enables:

- Type inspection and entity conversion for any PHP value
- Multiple rendering formats (HTML, Text, CLI)
- Custom dump definitions via `Dumpable` interface
- Object property introspection with visibility and metadata
- Stack trace rendering
- Binary data handling
- Circular reference detection
- Sensitive data protection

Nuance is designed to be integrated into larger debug tools (such as Glitch) and provides the core inspection and rendering functionality without a user interface.

### Non-Goals

- Nuance does not provide a user interface or debug toolbar
- It does not handle error reporting or exception handling
- It does not provide logging or monitoring capabilities
- It does not provide profiling or performance analysis
- It does not handle HTTP request/response debugging
- It does not provide database query inspection

## Role in the Ecosystem

### Cluster & Positioning

Nuance belongs to the **runtime** cluster, providing core inspection and rendering capabilities for debugging and development tools. It sits at the foundation of the observability stack, enabling other packages to display structured data dumps.

### Usage Contexts

Nuance is used for:

- Debug output and variable inspection
- Exception stack trace rendering
- Development tooling and debugging interfaces
- Data structure visualization
- Object introspection and property inspection
- Custom dump implementations for userland objects

## Public Surface

### Key Types

- **`Inspector`** — Class for inspecting values and converting them to Entity instances. Handles circular reference detection and object extension discovery.

- **`Renderer`** — Interface defining the contract for rendering Entity instances to strings. Provides methods for rendering all entity types, stack traces, signatures, and lists.

- **`RendererTrait`** — Trait providing default implementations for the Renderer interface. Used by concrete renderer implementations.

- **`Renderer\Html`** — HTML renderer implementation. Generates HTML markup with CSS classes for styling.

- **`Renderer\Text`** — Plain text renderer implementation. Uses RendererTrait defaults.

- **`Renderer\Cli`** — CLI renderer implementation with ANSI color formatting.

- **`Entity`** — Marker interface for all entity types.

- **`Entity\Value`** — Interface for entities that wrap a value.

- **`Entity\Structured`** — Interface for entities that can be opened/closed (arrays, objects).

- **`Entity\StructuredTrait`** — Trait providing default implementation for Structured interface.

- **`Entity\NativeObject`** — Entity representing a PHP object. Includes class hierarchy, properties, values, metadata, and sections.

- **`Entity\NativeArray`** — Entity representing a PHP array. Handles circular reference detection.

- **`Entity\NativeString`** — Entity representing a string value.

- **`Entity\Binary`** — Entity representing binary string data. Extends NativeString with hex conversion.

- **`Entity\NativeBoolean`** — Entity representing a boolean value.

- **`Entity\NativeInteger`** — Entity representing an integer value.

- **`Entity\NativeFloat`** — Entity representing a float value.

- **`Entity\NativeNull`** — Entity representing a null value.

- **`Entity\NativeResource`** — Entity representing a resource.

- **`Entity\ClassString`** — Entity representing a class name string.

- **`Entity\ConstOption`** — Entity representing an enum case or constant option.

- **`Entity\FlagSet`** — Entity representing a set of flag constants.

- **`Entity\Traceable`** — Interface for entities that can contain stack traces.

- **`Dumpable`** — Interface for objects that can provide custom dump information via `toNuanceEntity()`.

- **`DumpableVirtualProperty`** — Attribute for marking virtual properties as dumpable.

- **`SensitiveProperty`** — Attribute for marking properties as sensitive.

- **`Structure\Container`** — Class for organizing rendered content into sections (definition, text, values, properties, stack, meta, info).

- **`Structure\Section`** — Class representing a section within a container.

- **`Structure\SectionMap`** — Class for managing section enable/disable state.

- **`Structure\Property`** — Class representing an object property with visibility, virtual, and read-only flags.

- **`Structure\PropertyVisibility`** — Enum defining property visibility (Public, Protected, Private).

- **`Structure\ListStyle`** — Enum defining list rendering styles (Info, Meta, Props, Values).

- **`Structure\ClassList`** — Class for managing CSS class names.

- **`Structure\LazyType`** — Enum for lazy object types (Ghost, Proxy, Unknown).

- **`Reflection`** — Utility class for generating class, property, and function definitions from reflection.

- **`PrettyPath`** — Utility class for path prettification.

### Main Entry Points

- **`Inspector::inspect(mixed $value): Entity`** — Inspects a value and returns an Entity instance. Handles all PHP types and circular references.

- **`Inspector::reset(): void`** — Resets internal state (array and object ID tracking).

- **`Inspector::inspectClassMembers(object $object, NativeObject $entity, array $blackList = [], bool $asMeta = false): void`** — Static method for inspecting object properties via reflection.

- **`Renderer::render(mixed $value, int $level = 0, ?ClassList $classes = null): string`** — Renders a value to a string. Delegates to specific render methods based on entity type.

- **`Renderer::renderValue(mixed $value, int $level = 0, ?ClassList $classes = null): string`** — Renders any value, inspecting it first if needed.

- **`Renderer::renderObject(NativeObject $entity, int $level = 0, ?ClassList $classes = null): string`** — Renders an object entity with all sections.

- **`Renderer::renderArray(NativeArray $entity, int $level = 0, ?ClassList $classes = null): string`** — Renders an array entity.

- **`Renderer::renderStackTrace(Trace $trace, ?ClassList $classes = null): string`** — Renders a stack trace.

- **`Dumpable::toNuanceEntity(): NativeObject`** — Returns a custom NativeObject representation for the implementing object.

- **`NativeObject::setProperty(string $name, mixed $value, PropertyVisibility $visibility = Public, bool $virtual = false, bool $readOnly = false): void`** — Adds a property to the object entity.

- **`NativeObject::addProperty(Property $property): void`** — Adds a Property instance to the object entity.

- **`NativeObject::getHierarchy(): array`** — Returns the class hierarchy (parents + self).

- **`NativeObject::getExtensionClasses(): array`** — Returns all classes and interfaces for extension discovery.

- **`NativeObject::getInfoValues(): array`** — Returns info section values (class, location, parents, interfaces, traits, hash).

- **`Container::addSection(Section $section): void`** — Adds a section to the container.

- **`Container::getOpenSections(): array`** — Returns all open sections.

- **`Container::sortSections(): void`** — Sorts sections by priority.

- **`SectionMap::disable(string $key): void`** — Disables a section.

- **`SectionMap::isEnabled(string $key): bool`** — Checks if a section is enabled.

## Dependencies

### Decode Labs

- **`coercion`** — Used for type coercion throughout the package.

- **`enumerable`** — Used for enum implementations (ListStyle, PropertyVisibility).

- **`remnant`** — Used for stack trace representation and rendering.

### External

- None (pure PHP implementation).

## Behaviour & Contracts

### Invariants

- Entities are immutable after creation (except NativeObject which allows property addition)
- Circular references are detected and marked as referenced
- Object IDs are tracked to detect duplicate references
- Array references are tracked using hash-based cookies
- Sensitive properties are replaced with SensitiveParameterValue
- Virtual properties are skipped unless marked with DumpableVirtualProperty
- Extension classes are discovered via namespace-based class resolution
- Sections can be enabled/disabled via SectionMap
- Renderers must handle all entity types
- HTML renderer escapes all output
- CLI renderer uses ANSI color codes
- Text renderer uses plain text output

### Input & Output Contracts

- **`Inspector::inspect(mixed $value): Entity`** — Returns an Entity instance. Handles null, bool, int, float, string, resource, array, and object types. Detects binary strings and class name strings. Tracks circular references.

- **`Renderer::render(mixed $value, int $level = 0, ?ClassList $classes = null): string`** — Returns rendered string. Inspects value if not already an Entity. Delegates to specific render methods.

- **`Renderer::renderObject(NativeObject $entity, int $level = 0, ?ClassList $classes = null): string`** — Returns rendered object string. Includes all enabled sections (definition, text, values, properties, stack, meta, info). Handles referenced objects.

- **`Renderer::renderArray(NativeArray $entity, int $level = 0, ?ClassList $classes = null): string`** — Returns rendered array string. Includes length and values. Handles circular references.

- **`Renderer::renderStackTrace(Trace $trace, ?ClassList $classes = null): string`** — Returns rendered stack trace. Includes frame numbers, signatures, locations, and arguments.

- **`Dumpable::toNuanceEntity(): NativeObject`** — Returns a NativeObject instance. Should populate displayName, itemName, sensitive, length, definition, text, values, properties, and meta.

- **`NativeObject::setProperty(string $name, mixed $value, PropertyVisibility $visibility, bool $virtual, bool $readOnly): void`** — Adds a property. Creates a Property instance and adds it to the properties array.

- **`NativeObject::getHierarchy(): array`** — Returns array of class names in hierarchy order (parents reversed, then self).

- **`Container::addSection(Section $section): void`** — Adds section to sections array keyed by section key.

- **`Container::getOpenSections(): array`** — Returns filtered array of sections where open is true.

- **`Container::sortSections(): void`** — Sorts sections array by priority using uasort.

- **`SectionMap::disable(string $key): void`** — Disables section by removing from enabled set.

- **`SectionMap::isEnabled(string $key): bool`** — Returns true if section is in enabled set.

## Error Handling

Nuance uses standard PHP exceptions for error handling. Key exception types:

- **`UnexpectedValueException`** — Thrown when an invalid entity type is encountered during rendering or when control character unpacking fails.

- **`RuntimeException`** — Thrown by Reflection utilities when date operations fail.

Exceptions preserve context and include detailed error messages. Rendering never throws exceptions for invalid input; it falls back to string representation.

## Configuration & Extensibility

### Extension Points

- **Custom Renderers** — Implement `Renderer` interface to provide custom output formats.

- **Custom Entity Types** — Implement `Entity` interface and register with Inspector extensions.

- **Custom Object Dumps** — Implement `Dumpable` interface to provide custom dump information.

- **Object Extensions** — Create classes in `Entity\NativeObject\{ClassName}` namespace to provide specialized object handling.

- **Section Management** — Use `SectionMap` to enable/disable sections per object.

- **Property Attributes** — Use `DumpableVirtualProperty` and `SensitiveProperty` attributes to control property visibility.

### Configuration

- **Inspector Extensions** — Extension classes are discovered automatically via namespace resolution.

- **Renderer Selection** — Choose renderer based on output context (HTML for web, CLI for terminal, Text for plain output).

- **Section Control** — Sections can be disabled per object via `SectionMap::disable()`.

- **Sensitive Data** — Mark entire objects or individual properties as sensitive to prevent data display.

- **Circular Reference Handling** — Circular references are automatically detected and marked as referenced.

- **Lazy Object Detection** — Lazy objects (ghost/proxy) are automatically detected and marked.

- **Path Prettification** — Detected at runtime if Monarch is installed, used for prettifying file paths in stack traces.

## Interactions with Other Packages

- **Glitch** — Uses Nuance for exception and variable rendering in debug interface.

- **Remnant** — Provides stack trace representation used by Nuance for rendering.

- **Monarch** — Detected at runtime if installed, used for path prettification in stack traces.

- **Coercion** — Used for type coercion throughout the package.

- **Enumerable** — Used for enum implementations.

## Usage Examples

### Basic Inspection and Rendering

```php
use DecodeLabs\Nuance\Inspector;
use DecodeLabs\Nuance\Renderer\Html as HtmlRenderer;

$inspector = new Inspector();
$renderer = new HtmlRenderer();

$value = ['foo' => 'bar', 'baz' => ['qux' => 'quux']];
$entity = $inspector->inspect($value);
$output = $renderer->render($entity);
```

### Custom Object Dumps

```php
use DecodeLabs\Nuance\Dumpable;
use DecodeLabs\Nuance\Entity\NativeObject;

class MyCustomObject implements Dumpable
{
    public function toNuanceEntity(): NativeObject
    {
        $output = new NativeObject($this);
        
        $output->displayName = 'Custom\\Object';
        $output->itemName = 'option-1';
        $output->sensitive = false;
        $output->length = 42;
        $output->definition = '<xml>MyCustomObject</xml>';
        $output->text = 'My custom object text representation';
        $output->values = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];
        $output->valueKeys = true;
        
        $output->setProperty(
            name: 'property1',
            value: 'value1',
            visibility: 'protected',
            virtual: false,
            readOnly: true
        );
        
        $output->meta = [
            'created_at' => '2023-10-01',
            'updated_at' => '2023-10-02',
        ];
        
        $output->sections->disable('info');
        
        return $output;
    }
}
```

### Stack Trace Rendering

```php
use DecodeLabs\Nuance\Renderer\Html as HtmlRenderer;
use DecodeLabs\Remnant\Trace;

$renderer = new HtmlRenderer();
$trace = Trace::capture();
$output = $renderer->renderStackTrace($trace);
```

### Section Management

```php
use DecodeLabs\Nuance\Entity\NativeObject;

$entity = new NativeObject($object);

// Disable sections
$entity->sections->disable('info');
$entity->sections->disable('meta');

// Check if enabled
if ($entity->sections->isEnabled('properties')) {
    // Properties section is enabled
}
```

### Property Attributes

```php
use DecodeLabs\Nuance\DumpableVirtualProperty;
use DecodeLabs\Nuance\SensitiveProperty;

class MyObject
{
    #[DumpableVirtualProperty]
    public string $virtualProperty;
    
    #[SensitiveProperty]
    public string $password;
}
```

### CLI Rendering

```php
use DecodeLabs\Nuance\Renderer\Cli;

$renderer = new Cli();
$output = $renderer->render($value);
// Output includes ANSI color codes
```

### Text Rendering

```php
use DecodeLabs\Nuance\Renderer\Text;

$renderer = new Text();
$output = $renderer->render($value);
// Plain text output
```

### Container and Sections

```php
use DecodeLabs\Nuance\Structure\Container;
use DecodeLabs\Nuance\Structure\Section;

$container = new Container(
    type: 'object',
    id: 'obj-1',
    objectId: 123,
    open: true,
    sensitive: false
);

$container->renderedName = 'MyClass';

$section = new Section('properties', priority: 0, open: true);
$section->renderedContent = '...';
$container->addSection($section);

$output = $renderer->renderContainer($container);
```

## Implementation Notes (for Contributors)

### Architecture

- **Entity Pattern** — Values are converted to Entity instances that encapsulate type information and metadata.

- **Renderer Pattern** — Renderers implement the Renderer interface and use RendererTrait for common functionality.

- **Extension Discovery** — Object extensions are discovered via namespace-based class resolution in `Entity\NativeObject\{ClassName}`.

- **Circular Reference Detection** — Arrays use hash-based cookies, objects use object ID tracking with a stack.

- **Lazy Object Detection** — Uses `var_dump` output to detect lazy ghost/proxy objects.

- **Section Management** — Sections are organized in a Container with enable/disable state managed by SectionMap.

- **Property Introspection** — Uses reflection to discover properties, respecting visibility, virtual, and read-only flags.

- **Sensitive Data Protection** — Sensitive properties are replaced with SensitiveParameterValue, entire objects can be marked sensitive.

- **Path Prettification** — Uses Monarch paths if available to convert absolute paths to aliases.

### Performance Considerations

- Entity instances are created once per value
- Circular references are detected to prevent infinite loops
- Extension classes are cached per class name
- Array references use lightweight hash-based tracking
- Renderers use efficient string concatenation
- HTML renderer escapes output to prevent XSS

### Design Decisions

- **Entity Pattern** — Separating inspection from rendering allows multiple renderers for the same entity.

- **Extension Discovery** — Namespace-based discovery allows specialized handling without explicit registration.

- **Section Management** — Flexible section system allows customization per object while maintaining defaults.

- **Sensitive Data Protection** — Attribute-based marking provides fine-grained control over data visibility.

- **Renderer Trait** — Common functionality is shared via trait to reduce duplication.

- **Circular Reference Detection** — Lightweight tracking prevents infinite loops without heavy memory usage.

- **Lazy Object Detection** — Using var_dump provides reliable detection of lazy objects.

- **Path Prettification** — Optional Monarch integration provides better UX without hard dependency.

## Testing & Quality

**Code Quality:** 4/5 — Good, mature codebase with comprehensive functionality and solid architecture.

**README Quality:** 1/5 — Minimal documentation with basic usage examples.

**Documentation:** 0/5 — No formal documentation beyond README.

**Tests:** 0/5 — No test suite currently.

See `composer.json` for supported PHP versions.

## Roadmap & Future Ideas

- Enhanced documentation and API reference
- Test suite implementation
- Additional renderer formats (JSON, XML)
- Performance optimizations for large data structures
- Additional entity types for specialized PHP types
- Enhanced stack trace rendering options
- Custom section types
- Property filtering and transformation
- Memory usage optimizations
- Caching for repeated inspections

## References

- [Decode Labs Chorus](https://github.com/decodelabs/chorus)
- [Nuance Repository](https://github.com/decodelabs/nuance)
- [Glitch Repository](https://github.com/decodelabs/glitch)
- [Remnant Repository](https://github.com/decodelabs/remnant)

