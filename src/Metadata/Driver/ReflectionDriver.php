<?php

namespace TSantos\Serializer\Metadata\Driver;

use Metadata\Driver\DriverInterface;
use Metadata\MergeableClassMetadata;
use TSantos\Serializer\Metadata\PropertyMetadata;
use TSantos\Serializer\Metadata\VirtualPropertyMetadata;
use TSantos\Serializer\TypeGuesser;

/**
 * Class ReflectionDriver
 *
 * @package Serializer\Metadata\Driver
 * @author Tales Santos <tales.maxmilhas@gmail.com>
 */
class ReflectionDriver implements DriverInterface
{
    /**
     * @var TypeGuesser
     */
    private $typeGuesser;

    public function __construct(TypeGuesser $typeGuesser)
    {
        $this->typeGuesser = $typeGuesser;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $metadata = new MergeableClassMetadata($name = $class->name);

        /** @var \ReflectionProperty $property */
        foreach ($class->getProperties() as $property) {
            $propertyMetadata = new PropertyMetadata($name, $property->getName());
            $getter = 'get' . ucfirst($property->getName());

            if ($class->hasMethod($getter)) {
                // type from getter
                $method = new VirtualPropertyMetadata($name, $getter);
                $type = $this->typeGuesser->guessVirtualProperty($method, 'string');
                $propertyMetadata->accessor = $getter . '()';
            } elseif ($property->isPublic()) {
                // type from public property
                $type = $this->typeGuesser->guessProperty($propertyMetadata, 'string');
                $propertyMetadata->accessor = $property->getName();
            } else {
                continue;
            }

            $propertyMetadata->type = $type;
            $metadata->addPropertyMetadata($propertyMetadata);
        }

        return $metadata;
    }
}
