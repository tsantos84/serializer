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
use Symfony\Component\Yaml\Yaml;
use TSantos\Serializer\Exception\MappingException;
use TSantos\Serializer\Metadata\PropertyMetadata;
use TSantos\Serializer\Metadata\VirtualPropertyMetadata;
use TSantos\Serializer\TypeGuesser;

/**
 * Class YamlDriver
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class YamlDriver extends AbstractFileDriver
{
    /**
     * @var TypeGuesser
     */
    private $typeGuesser;

    /**
     * YamlDriver constructor.
     * @param FileLocatorInterface $fileLocator
     * @param TypeGuesser $typeGuesser
     */
    public function __construct(FileLocatorInterface $fileLocator, TypeGuesser $typeGuesser)
    {
        $this->typeGuesser = $typeGuesser;
        parent::__construct($fileLocator);
    }

    protected function loadMetadataFromFile(\ReflectionClass $class, $file)
    {
        if (!class_exists('Symfony\Component\Yaml\Yaml')) {
            throw new \RuntimeException('Yaml parser was not found. Did you added `symfony/yaml` to your project dependency?');
        }

        $config = Yaml::parse(file_get_contents($file));

        if (!isset($config[$class->name])) {
            throw new MappingException('There is no mapping for class ' . $class->name);
        }

        $mapping = $config[$class->name];

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

    protected function getExtension()
    {
        return 'yaml';
    }
}
