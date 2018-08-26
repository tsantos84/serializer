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

    public function serialize()
    {
        return \serialize([
            $this->name,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->createdAt,
            $this->baseClass,
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
            $this->baseClass) = \unserialize($str);

        $this->reflection = new \ReflectionClass($this->name);
    }

    public function hasProperties(): bool
    {
        return \count($this->propertyMetadata) > 0 || \count($this->methodMetadata) > 0;
    }

    public function all(): array
    {
        return \array_merge($this->propertyMetadata, $this->methodMetadata);
    }

    public function getWritableProperties(): array
    {
        return \array_filter($this->propertyMetadata, function (PropertyMetadata $propertyMetadata) {
            return !$propertyMetadata->readOnly;
        });
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
