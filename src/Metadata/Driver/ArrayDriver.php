<?php

namespace TSantos\Serializer\Metadata\Driver;

use Metadata\Driver\DriverInterface;
use Metadata\MergeableClassMetadata;
use TSantos\Serializer\Exception\MappingException;
use TSantos\Serializer\Metadata\PropertyMetadata;
use TSantos\Serializer\Metadata\VirtualPropertyMetadata;
use TSantos\Serializer\TypeGuesser;

class ArrayDriver implements DriverInterface
{
    /**
     * @var array
     */
    private $mapping;

    /**
     * @var TypeGuesser
     */
    private $typeGuesser;

    /**
     * ArrayDriver constructor.
     * @param $mapping
     */
    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
        $this->typeGuesser = new TypeGuesser();
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        if (!isset($this->mapping[$class->name])) {
            throw new MappingException('There is no mapping for class ' . $class->name);
        }

        $mapping = $this->mapping[$class->name];

        $metadata = new MergeableClassMetadata($class->getName());

        foreach ($mapping['properties'] ?? [] as $name => $map) {
            $property = new PropertyMetadata($class->getName(), $name);

            $property->getter = $map['getter'] ?? 'get' . ucfirst($name);
            $property->getterRef = new \ReflectionMethod($class->getName(), $property->getter);
            $property->type = $map['type'] ?? $this->typeGuesser->guessProperty($property, 'string');
            $property->exposeAs = $map['exposeAs'] ?? $name;
            $property->groups = (array)($map['groups'] ?? ['Default']);

            $metadata->addPropertyMetadata($property);
        }

        foreach ($mapping['virtual_properties'] ?? [] as $name => $map) {
            $method = $map['method'] ?? 'get' . ucfirst($name);

            $property = new VirtualPropertyMetadata($class->name, $method);
            $property->type = $map['type'] ?? $this->typeGuesser->guessVirtualProperty($property, 'string');
            $property->exposeAs = $map['exposeAs'] ?? $name;
            $property->groups = (array)($map['groups'] ?? ['Default']);
            $metadata->addMethodMetadata($property);
        }

        return $metadata;
    }
}
