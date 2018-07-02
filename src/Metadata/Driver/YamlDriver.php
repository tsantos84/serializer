<?php

declare(strict_types=1);
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
use Symfony\Component\Yaml\Yaml;
use TSantos\Serializer\Exception\MappingException;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\Metadata\PropertyMetadata;
use TSantos\Serializer\Metadata\VirtualPropertyMetadata;

/**
 * Class YamlDriver.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class YamlDriver extends AbstractFileDriver
{
    protected function loadMetadataFromFile(\ReflectionClass $class, $file)
    {
        if (!\class_exists('Symfony\Component\Yaml\Yaml')) {
            throw new \RuntimeException(
                'Yaml parser was not found. Did you added `symfony/yaml` to your project dependency?'
            );
        }

        $config = Yaml::parse(\file_get_contents($file));

        if (!isset($config[$class->name])) {
            throw new MappingException('There is no mapping for class '.$class->name);
        }

        $mapping = $config[$class->name];

        $metadata = new ClassMetadata($class->getName());

        if (isset($mapping['baseClass'])) {
            $metadata->baseClass = $mapping['baseClass'];
        }

        foreach ($mapping['properties'] ?? [] as $name => $map) {
            $property = new PropertyMetadata($class->getName(), $name);

            if (isset($map['getter'])) {
                $property->setGetter($map['getter']);
            }

            if (isset($map['setter'])) {
                $property->setGetter($map['setter']);
            }

            if (isset($map['exposeAs'])) {
                $property->exposeAs = $map['exposeAs'];
            }

            $property->readValueFilter = $map['readValue'] ?? null;
            $property->writeValueFilter = $map['writeValue'] ?? null;
            $property->type = $map['type'] ?? null;
            $property->groups = (array) ($map['groups'] ?? ['Default']);
            $property->readOnly = (bool) ($map['readOnly'] ?? false);
            $property->options = isset($map['options']) ? (array) $map['options'] : [];

            $metadata->addPropertyMetadata($property);
        }

        foreach ($mapping['virtualProperties'] ?? [] as $name => $map) {
            $method = $map['method'] ?? $name;

            $property = new VirtualPropertyMetadata($class->name, $method);
            $property->type = $map['type'] ?? null;
            $property->groups = (array) ($map['groups'] ?? ['Default']);
            $property->readValueFilter = $map['readValue'] ?? null;
            $property->options = isset($map['options']) ? (array) $map['options'] : [];
            $metadata->addMethodMetadata($property);

            if (isset($map['exposeAs'])) {
                $property->exposeAs = $map['exposeAs'];
            }
        }

        return $metadata;
    }

    protected function getExtension()
    {
        return 'yaml';
    }
}
