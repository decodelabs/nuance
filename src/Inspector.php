<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance;

use DecodeLabs\Coercion;
use DecodeLabs\Nuance\Entity\Binary;
use DecodeLabs\Nuance\Entity\ClassString;
use DecodeLabs\Nuance\Entity\NativeArray;
use DecodeLabs\Nuance\Entity\NativeBoolean;
use DecodeLabs\Nuance\Entity\NativeFloat;
use DecodeLabs\Nuance\Entity\NativeInteger;
use DecodeLabs\Nuance\Entity\NativeNull;
use DecodeLabs\Nuance\Entity\NativeObject;
use DecodeLabs\Nuance\Entity\NativeResource;
use DecodeLabs\Nuance\Entity\NativeString;
use DecodeLabs\Nuance\SensitiveProperty;
use DecodeLabs\Nuance\Structure\PropertyVisibility;
use ReflectionClass;
use SensitiveParameterValue;

class Inspector
{
    /**
     * @var array<class-string,?class-string<NativeObject>>
     */
    protected array $extensions = [];

    /**
     * @var array<string,int>
     */
    protected array $arrayIds = [];

    /**
     * @var array<int,string>
     */
    protected array $objectIds = [];

    /**
     * @var array<int>
     */
    protected array $objectStack = [];

    public function reset(): void
    {
        $this->arrayIds = [];
        $this->objectIds = [];
    }

    public function inspect(
        mixed $value
    ): Entity {
        // Entity
        if($value instanceof Entity) {
            return $value;
        }

        // Null
        if($value === null) {
            return new NativeNull();
        }

        // Bool
        if(is_bool($value)) {
            return new NativeBoolean($value);
        }

        // Int
        if(is_int($value)) {
            return new NativeInteger($value);
        }

        // Float
        if(is_float($value)) {
            return new NativeFloat($value);
        }

        // String
        if(is_string($value)) {
            // Binary string
            if (
                $value !== '' &&
                !preg_match('//u', $value)
            ) {
                return new Binary($value);

            }

            $isPossibleClass = preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(\\\\[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)*$/', $value);
            $loadClasses = str_contains($value, '\\');

            // Class name
            if (
                $isPossibleClass &&
                (
                    class_exists($value, $loadClasses) ||
                    interface_exists($value, $loadClasses) ||
                    trait_exists($value, $loadClasses)
                )
            ) {
                return new ClassString($value);
            }


            // Standard string
            return new NativeString($value);
        }

        // Resource
        if (is_resource($value)) {
            return new NativeResource($value);
        }

        // Array
        if (is_array($value)) {
            $entity = new NativeArray($value);

            if(!isset($this->arrayIds[$entity->hash])) {
                $this->arrayIds[$entity->hash] = count($this->arrayIds);
            }

            $entity->referenceId = $this->arrayIds[$entity->hash];
            return $entity;
        }

        // Object
        if (is_object($value)) {
            $objectId = spl_object_id($value);

            if(in_array($objectId, $this->objectStack)) {
                $entity = new NativeObject($value);
                $entity->referenced = true;
                return $entity;
            }

            $this->objectStack[] = $objectId;

            $entity = $this->inspectObject($value);

            array_pop($this->objectStack);

            if(!isset($this->objectIds[$entity->objectId])) {
                $entity->referenced = false;
                $this->objectIds[$entity->objectId] = $entity->id;
            } else {
                $entity->referenced = true;
                $entity->id = $this->objectIds[$entity->objectId];
            }

            return $entity;
        }

        return new NativeString(Coercion::toString($value));
    }

    private function inspectObject(
        object $value
    ): NativeObject {
        if ($value instanceof Dumpable) {
            return $value->toNuanceEntity();
        }

        // Extension
        if($entity = $this->extendObject($value)) {
            return $entity;
        }

        $entity = new NativeObject($value);

        // Debug info
        if(method_exists($value, '__debugInfo')) {
            $entity->values = Coercion::asArray($value->__debugInfo());
            return $entity;
        }

        // Reflection members
        $this->inspectClassMembers($value, $entity);

        return $entity;
    }


    private function extendObject(
        object $value,
    ): ?NativeObject {
        $class = get_class($value);

        if(array_key_exists($class, $this->extensions)) {
            $extensionClass = $this->extensions[$class];

            if($extensionClass === null) {
                return null;
            }
        } else {
            $refBase = new ReflectionClass($value);
            $extensions = $interfaces = [];

            while (true) {
                $interfaces = array_merge($interfaces, $refBase->getInterfaceNames());

                if (!$parent = $refBase->getParentClass()) {
                    break;
                }

                $parentName = $parent->getName();

                if(
                    !str_contains($parentName, '@anonymous') &&
                    !in_array($parentName, $extensions)
                ) {
                    $extensions[] = $parentName;
                }

                $refBase = $parent;
            }

            $root = [];

            if(!str_contains($class, '@anonymous')) {
                $root[] = $class;
            }

            $extensions = array_unique(
                array_merge($root, array_reverse($extensions), array_reverse($interfaces))
            );
            $extensionClass = null;

            usort($extensions, function($a, $b) {
                return count(explode('\\', $b)) <=> count(explode('\\', $a));
            });

            foreach($extensions as $subClass) {
                $subClass = NativeObject::class . '\\' . $subClass;

                if(
                    class_exists($subClass) &&
                    ($ref = new ReflectionClass($subClass))->isInstantiable() &&
                    $ref->isSubclassOf(NativeObject::class)
                ) {

                    $extensionClass = $subClass;
                    break;
                }
            }

            if($extensionClass === null) {
                return $this->extensions[$class] = null;
            }
        }

        /** @var class-string<NativeObject> $extensionClass */
        return new $extensionClass($value);
    }

    /**
     * @param array<string> $blackList
     */
    public static function inspectClassMembers(
        object $object,
        NativeObject $entity,
        array $blackList = [],
        bool $asMeta = false
    ): void {
        foreach ($entity->getHierarchy() as $class) {
            $reflection = new ReflectionClass($class);

            foreach ($reflection->getProperties() as $property) {
                if ($property->isStatic()) {
                    continue;
                }

                $property->setAccessible(true);
                $name = $property->getName();

                if (in_array($name, $blackList)) {
                    continue;
                }



                if (
                    $asMeta &&
                    isset($entity->meta[$name])
                ) {
                    continue;
                } elseif ($entity->hasProperty($name)) {
                    continue;
                } elseif(
                    $property->isVirtual() &&
                    empty($property->getAttributes(DumpableVirtualProperty::class))
                ) {
                    continue;
                }


                // Get value
                if ($property->isInitialized($object)) {
                    $value = $property->getValue($object);
                } else {
                    $value = null;
                }

                // Check sensitive
                if (!empty($property->getAttributes(SensitiveProperty::class))) {
                    $value = new SensitiveParameterValue($object);
                }

                if ($asMeta) {
                    $entity->meta[$name] = $value;
                    continue;
                }

                $entity->setProperty(
                    name: $name,
                    value: $value,
                    visibility: match(true) {
                        $property->isProtected() => PropertyVisibility::Protected,
                        $property->isPrivate() => PropertyVisibility::Private,
                        default => PropertyVisibility::Public
                    },
                    virtual: $property->isVirtual(),
                    readOnly:
                        $property->isPrivateSet() ||
                        $property->isProtectedSet() ||
                        $property->isReadOnly(),
                );
            }
        }
    }
}
