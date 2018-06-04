<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Serializer\Metadata\Configurator;

use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\Metadata\ConfiguratorInterface;
use TSantos\Serializer\Metadata\PropertyMetadata;

/**
 * Class TypeConfigurator
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class TypeConfigurator implements ConfiguratorInterface
{
    public function configure(ClassMetadata $classMetadata): void
    {
        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {
            if (null !== $propertyMetadata->type) {
                continue;
            }

            $this->doConfigureProperty($classMetadata, $propertyMetadata);
        }

        foreach ($classMetadata->methodMetadata as $methodMetadata) {
            if (null !== $methodMetadata->type) {
                continue;
            }

            $methodMetadata->type = $this->readTypeFromMethod($methodMetadata->reflection);
        }
    }

    private function doConfigureProperty(ClassMetadata $classMetadata, PropertyMetadata $propertyMetadata): void
    {
        $ucName = ucfirst($propertyMetadata->name);
        $getters = ['get' . $ucName, 'is' . $ucName, 'has' . $ucName];
        $getter = null;

        foreach ($getters as $getterName) {
            if ($classMetadata->reflection->hasMethod($getterName)) {
                $getter = $classMetadata->reflection->getMethod($getterName);
                break;
            }
        }

        // there is a getter method
        if (null !== $getter && null !== $type = $this->readTypeFromMethod($getter)) {
            $propertyMetadata->type = $type;
            return;
        }

        // guess type from property's default value
        $defaultProperties = $classMetadata->reflection->getDefaultProperties();

        if (isset($defaultProperties[$propertyMetadata->name])) {
            $propertyMetadata->type = $this->translate(gettype($defaultProperties[$propertyMetadata->name]));
            return;
        }

        // impossible to guess the type from its getter and default value, so lets try to guess from property's doc-block
        if (null !== $type = $this->readTypeFromPropertyDocBlock($propertyMetadata->reflection)) {
            $propertyMetadata->type = $this->translate($type);
            return;
        }

        // defaults to 'string'
        $propertyMetadata->type =  'string';
    }

    private function readTypeFromMethod(\ReflectionMethod $method): ?string
    {
        // try to read from its return type
        if (null !== $returnType = $method->getReturnType()) {
            $type = $returnType->getName();
            if ($returnType->isBuiltin()) {
                $type = $this->translate($type);
            }
            return $type;
        }

        // try to read from its doc-block return type
        if (null !== $returnType = $this->readTypeFromGetterDocBlock($method)) {
            return $this->translate($returnType);
        }

        return null;
    }

    private function readTypeFromPropertyDocBlock(\ReflectionProperty $property): ?string
    {
        if (false !== $docBlock = $property->getDocComment()) {
            return $this->readFromDocComment($docBlock);
        }

        return null;
    }

    private function readTypeFromGetterDocBlock(\ReflectionMethod $getter): ?string
    {
        if (false !== $docBlock = $getter->getDocComment()) {
            return $this->readFromDocComment($docBlock);
        }

        return null;
    }

    private function readFromDocComment(string $docComment): ?string
    {
        if (preg_match('/@(return|var)\s+([^\s]+)/', $docComment, $matches)) {
            list(,,$type) = $matches;
            return $type;
        }

        return null;
    }

    private function translate(string $type): string
    {
        switch ($type) {
            case 'int':
                return 'integer';
            case 'bool':
                return 'boolean';
            case 'mixed':
                return 'string';
            default:
                return $type;
        }
    }
}
