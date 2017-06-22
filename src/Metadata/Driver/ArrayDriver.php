<?php

namespace Serializer\Metadata\Driver;

use Metadata\ClassMetadata;
use Metadata\Driver\DriverInterface;
use Serializer\Metadata\PropertyMetadata;

class ArrayDriver implements DriverInterface
{
    /**
     * @var array
     */
    private $mapping;

    /**
     * ArrayDriver constructor.
     * @param $mapping
     */
    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        if (!isset($this->mapping[$class->name])) {
            throw new \Exception('There is no mapping for class ' . $class->name);
        }

        $mapping = $this->mapping[$class->name];

        $metadata = new ClassMetadata($class->getName());

        foreach ($mapping['properties'] as $name => $map) {

            $property = new PropertyMetadata($class->getName(), $name);

            $property->type = $map['type'] ?? 'string';
            $property->getter = $map['getter'] ?? 'get' . ucfirst($name);
            $property->exposeAs = $map['exposeAs'] ?? $name;

            $metadata->addPropertyMetadata($property);
        }

        return $metadata;
    }
}
