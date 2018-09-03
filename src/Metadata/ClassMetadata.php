<?php

declare(strict_types=1);

/*
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Serializer\Metadata;

use Metadata\MergeableClassMetadata;
use Metadata\MergeableInterface;
use Metadata\PropertyMetadata as JMSPropertyMetadata;

/**
 * Class ClassMetadata.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class ClassMetadata extends MergeableClassMetadata
{
    public $baseClass;

    public $discriminatorField;

    public $discriminatorMapping;

    public $hydratorConstructArgs = [];

    public $constructArgs = [];

    /**
     * ClassMetadata constructor.
     *
     * @param $name
     */
    public function __construct($name)
    {
        parent::__construct($name);

        if (null !== $construct = $this->reflection->getConstructor()) {
            foreach ($construct->getParameters() as $parameter) {
                $this->constructArgs[$parameter->name] = $parameter->getPosition();
            }
        }
    }

    public function serialize()
    {
        return \serialize([
            $this->name,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->createdAt,
            $this->baseClass,
            $this->constructArgs,
        ]);
    }

    public function unserialize($str)
    {
        list(
            $this->name,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->createdAt,
            $this->baseClass,
            $this->constructArgs) = \unserialize($str);

        $this->reflection = new \ReflectionClass($this->name);
    }

    public function merge(MergeableInterface $object)
    {
        parent::merge($object);
        $this->constructArgs = $object->constructArgs;
    }

    public function hasProperties(): bool
    {
        return \count($this->propertyMetadata) > 0 || \count($this->methodMetadata) > 0;
    }

    public function addPropertyMetadata(JMSPropertyMetadata $metadata)
    {
        if (isset($this->constructArgs[$metadata->name])) {
            $metadata->isConstructArg = true;
        }

        parent::addPropertyMetadata($metadata);
    }

    public function all(): array
    {
        return \array_merge($this->propertyMetadata, $this->methodMetadata);
    }

    public function getWritableProperties(): array
    {
        return \array_filter($this->propertyMetadata, function (PropertyMetadata $propertyMetadata) {
            if ($propertyMetadata->readOnly) {
                return false;
            }

            if (!$this->canBeInstantiatedThroughConstructor()) {
                return true;
            }

            return !$propertyMetadata->isConstructArg;
        });
    }

    public function getConstructProperties(): array
    {
        return \array_filter($this->propertyMetadata, function (PropertyMetadata $propertyMetadata) {
            return $propertyMetadata->isConstructArg;
        });
    }

    public function canBeInstantiatedThroughConstructor(): bool
    {
        if (null === $constructor = $this->reflection->getConstructor()) {
            return false;
        }

        if ($constructor->isStatic() || $constructor->isPrivate() || $constructor->isProtected()) {
            return false;
        }

        if ($constructor->getNumberOfRequiredParameters() > 0) {
            return \count($this->getConstructProperties()) >= $constructor->getNumberOfRequiredParameters();
        }

        return true;
    }

    public function setDiscriminatorMap(string $field, array $mapping)
    {
        $this->discriminatorField = $field;
        $this->discriminatorMapping = $mapping;
    }

    public function isAbstract(): bool
    {
        return $this->reflection->isAbstract() || $this->reflection->isInterface();
    }
}
