<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Serializer\Metadata\Driver;

use Metadata\Driver\DriverInterface;
use Metadata\MergeableClassMetadata;
use TSantos\Serializer\Exception\MappingException;
use TSantos\Serializer\Metadata\PropertyMetadata;
use TSantos\Serializer\Metadata\VirtualPropertyMetadata;
use TSantos\Serializer\TypeGuesser;

/**
 * Class InMemoryDriver
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class InMemoryDriver implements DriverInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var TypeGuesser
     */
    private $typeGuesser;

    /**
     * ArrayDriver constructor.
     * @param $config
     * @param $guesser
     */
    public function __construct(array $config, TypeGuesser $guesser)
    {
        $this->config = $config;
        $this->typeGuesser = $guesser;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        if (!isset($this->config[$class->name])) {
            throw new MappingException('There is no mapping for class ' . $class->name);
        }

        $mapping = $this->config[$class->name];

        $metadata = new MergeableClassMetadata($class->getName());

        foreach ($mapping['properties'] ?? [] as $name => $map) {
            $property = new PropertyMetadata($class->getName(), $name);

            $property->getter = $map['getter'] ?? 'get' . ucfirst($name);
            $property->getterRef = new \ReflectionMethod($class->getName(), $property->getter);
            $property->modifier = $map['modifier'] ?? null;
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
            $property->modifier = $map['modifier'] ?? null;
            $metadata->addMethodMetadata($property);
        }

        return $metadata;
    }

}
