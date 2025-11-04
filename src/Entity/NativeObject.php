<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity;

use DecodeLabs\Monarch;
use DecodeLabs\Nuance\Structure\LazyType;
use DecodeLabs\Nuance\Structure\Property;
use DecodeLabs\Nuance\Structure\PropertyVisibility;
use DecodeLabs\Nuance\Structure\SectionMap;
use ReflectionClass;
use ReflectionObject;
use Stringable;
use Throwable;

class NativeObject implements Structured
{
    use StructuredTrait;

    public string $id {
        get => $this->id ??= str_replace(['.', '\\', '~', '@'], '-', uniqid($this->class . '-', true));
    }

    public string $displayName {
        get {
            if (isset($this->displayName)) {
                return $this->displayName;
            }

            $name = $this->className;

            if (str_contains($name, "class@anonymous\x00")) {
                $ref = new ReflectionClass($this->class);

                if ($parent = $ref->getParentClass()) {
                    $name = $parent->getName() . '@anonymous';
                } else {
                    $name = '@anonymous';
                }
            } else {
                $parts = explode('\\', $name);
                array_pop($parts);
                $parentNs = array_pop($parts);

                if (!empty($parentNs)) {
                    foreach ($this->interfaces as $interface) {
                        if (str_ends_with($interface, '\\' . $parentNs)) {
                            $name = $parentNs . '\\' . $name;
                            break;
                        }
                    }
                }
            }

            return $this->displayName = $name;
        }
    }

    public protected(set) int $objectId;
    public protected(set) string $hash;

    /**
     * @var class-string
     */
    public protected(set) string $class;
    public protected(set) string $className;
    public protected(set) ?string $namespace = null;
    public protected(set) bool $internal;

    /**
     * @var list<class-string>
     */
    public protected(set) array $parents;

    /**
     * @var list<class-string>
     */
    public protected(set) array $interfaces;

    /**
     * @var list<class-string>
     */
    public protected(set) array $traits;

    public protected(set) ?string $file = null;
    public protected(set) ?int $startLine = null;
    public protected(set) ?int $endLine = null;

    public protected(set) ?LazyType $lazy = null;
    public ?string $itemName = null;
    public string|Stringable|null $text = null;
    public string|Stringable|null $definition = null;
    public bool $sensitive = false;
    public ?int $length = null;

    /**
     * @var array<string,mixed>
     */
    public array $meta = [];

    /**
     * @var array<string,Property>
     */
    public protected(set) array $properties = [];

    /**
     * @var array<mixed>
     */
    public array $values = [];
    public bool $valueKeys = true;

    public mixed $value {
        get => $this->values[array_key_first($this->values)] ?? null;
        set {
            $this->values = [$value];
            $this->valueKeys = false;
        }
    }

    public bool $referenced = false;

    public SectionMap $sections {
        get => $this->sections ??= new SectionMap();
    }

    public function __construct(
        object $object,
    ) {
        $ref = new ReflectionObject($object);
        $this->objectId = spl_object_id($object);
        $this->hash = spl_object_hash($object);
        $this->class = $ref->getName();
        $this->className = $ref->getShortName();
        $namespace = $ref->getNamespaceName();

        if ($namespace === '') {
            $this->namespace = null;
        } else {
            $this->namespace = $namespace;
        }

        $this->internal = $ref->isInternal();

        $refBase = $ref;
        $this->parents = [];
        $this->interfaces = [];
        $this->traits = [];

        while (true) {
            $this->interfaces = array_merge($this->interfaces, $ref->getInterfaceNames());
            $this->traits = array_merge($this->traits, $ref->getTraitNames());

            if (!$parent = $refBase->getParentClass()) {
                break;
            }

            $this->parents[] = $parent->getName();
            $refBase = $parent;
        }

        $this->interfaces = array_values(array_reverse(array_unique($this->interfaces)));
        $this->traits = array_values(array_reverse(array_unique($this->traits)));


        sort($this->parents);
        sort($this->interfaces);
        sort($this->traits);

        if (!$this->internal) {
            $file = $ref->getFileName();
            $startLine = $ref->getStartLine();
            $endLine = $ref->getEndLine();
            $this->file = $file ?: null;
            $this->startLine = $startLine ?: null;
            $this->endLine = $endLine ?: null;
        }

        if ($ref->isUninitializedLazyObject($object)) {
            try {
                ob_start();
                var_dump($object);
                $export = (string)ob_get_clean();

                if (str_starts_with($export, 'lazy ghost')) {
                    $this->lazy = LazyType::Ghost;
                } elseif (str_starts_with($export, 'lazy proxy')) {
                    $this->lazy = LazyType::Proxy;
                } else {
                    $this->lazy = LazyType::Unknown;
                }
            } catch (Throwable $e) {
                $this->lazy = LazyType::Unknown;
            }
        }
    }


    public function setProperty(
        string $name,
        mixed $value,
        string|PropertyVisibility $visibility = PropertyVisibility::Public,
        bool $virtual = false,
        bool $readOnly = false
    ): void {
        if (is_string($visibility)) {
            $visibility = PropertyVisibility::from($visibility);
        }

        $this->addProperty(new Property(
            $name,
            $value,
            $visibility,
            $virtual,
            $readOnly
        ));
    }

    public function addProperty(
        Property $property
    ): void {
        $this->properties[$property->name] = $property;
    }

    public function getProperty(
        string $name
    ): ?Property {
        return $this->properties[$name] ?? null;
    }

    public function hasProperty(
        string $name
    ): bool {
        return isset($this->properties[$name]);
    }

    public function removeProperty(
        string $name
    ): void {
        unset($this->properties[$name]);
    }


    /**
     * @return list<class-string>
     */
    public function getHierarchy(): array
    {
        $hierarchy = array_reverse($this->parents);
        $hierarchy[] = $this->class;
        return $hierarchy;
    }


    /**
     * @return list<class-string>
     */
    public function getExtensionClasses(): array
    {
        return [$this->class] + $this->parents + $this->interfaces;
    }



    /**
     * @return array<string,string|array<string>>
     */
    public function getInfoValues(): array
    {
        $info['class'] = $this->class;

        if ($file = $this->file) {
            if (class_exists(Monarch::class)) {
                $file = Monarch::getPaths()->prettify($file);
            }

            $info['location'] = $file . ' : ' . $this->startLine;
        }

        if (!empty($this->parents)) {
            $info['parents'] = $this->parents;
        }

        if (!empty($this->interfaces)) {
            $info['interfaces'] = $this->interfaces;
        }

        if (!empty($this->traits)) {
            $info['traits'] = $this->traits;
        }

        $info['hash'] = $this->hash;

        return $info;
    }
}
