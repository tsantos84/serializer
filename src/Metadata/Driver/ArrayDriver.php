<?php

namespace Serializer\Metadata\Driver;

use Metadata\ClassMetadata;
use Metadata\Driver\DriverInterface;
use Serializer\Metadata\PropertyMetadata;
use Serializer\Metadata\VirtualPropertyMetadata;

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

        foreach ($mapping['properties'] ?? [] as $name => $map) {

            $property = new PropertyMetadata($class->getName(), $name);

            $property->type = $map['type'] ?? 'string';
            $property->getter = $map['getter'] ?? 'get' . ucfirst($name);
            $property->exposeAs = $map['exposeAs'] ?? $name;
            $property->groups = (array)($map['groups'] ?? ['Default']);

            $metadata->addPropertyMetadata($property);
        }

        foreach ($mapping['virtual_properties'] ?? [] as $name => $map) {

            $method = $map['method'] ?? 'get' . ucfirst($name);

            $property = new VirtualPropertyMetadata($class->name, $method);
            $property->type = $map['type'] ?? 'string';
            $property->exposeAs = $map['exposeAs'] ?? $name;
            $property->groups = (array)($map['groups'] ?? ['Default']);
            $metadata->addMethodMetadata($property);
        }

        return $metadata;
    }
}
