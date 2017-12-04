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

use Metadata\Driver\AbstractFileDriver;
use Metadata\Driver\FileLocatorInterface;
use Metadata\MergeableClassMetadata;
use TSantos\Serializer\Exception\MappingException;
use TSantos\Serializer\Metadata\PropertyMetadata;
use TSantos\Serializer\Metadata\VirtualPropertyMetadata;
use TSantos\Serializer\TypeGuesser;

class PhpDriver extends AbstractFileDriver
{
    /**
     * @var TypeGuesser
     */
    private $typeGuesser;

    /**
     * PhpDriver constructor.
     * @param FileLocatorInterface $locator
     * @param TypeGuesser $guesser
     */
    public function __construct(FileLocatorInterface $locator, TypeGuesser $guesser)
    {
        parent::__construct($locator);
        $this->typeGuesser = $guesser;
    }

    protected function loadMetadataFromFile(\ReflectionClass $class, $file)
    {
        $config = require $file;

        if (!is_array($config)) {
            throw new MappingException('Expected that the file "%s" returns an array, "%s" given', gettype($config));
        }

        if (!isset($config[$class->name])) {
            throw new MappingException('There is no mapping for class ' . $class->name);
        }

        $mapping = $config[$class->name];

        $metadata = new MergeableClassMetadata($class->getName());

        foreach ($mapping['properties'] ?? [] as $name => $map) {
            $property = new PropertyMetadata($class->getName(), $name);

            $getter = $map['getter'] ?? 'get' . ucfirst($name);
            $property->accessor = $getter . '()';
            $property->getterRef = new \ReflectionMethod($class->getName(), $getter);
            $property->modifier = $map['modifier'] ?? null;
            $property->setter = $map['setter'] ?? 'set' . ucfirst($name);
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

    protected function getExtension()
    {
        return 'php';
    }
}
