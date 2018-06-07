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

            $methodMetadata->type = $this->readTypeFromGetter($methodMetadata->reflection);
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

        // 1. guess type from getter method
        if (null !== $getter && null !== $type = $this->readTypeFromGetter($getter)) {
            $propertyMetadata->type = $type;
            return;
        }

        // 2. guess type from setter method
        $setter = 'set' . ucfirst($propertyMetadata->name);
        if ($classMetadata->reflection->hasMethod($setter)
            && null !== $type = $this->readTypeFromSetter($classMetadata->reflection->getMethod($setter)))
        {
            $propertyMetadata->type = $this->translate($type);
            return;
        }

        // 3. guess type from property's default value type
        $defaultProperties = $classMetadata->reflection->getDefaultProperties();

        if (isset($defaultProperties[$propertyMetadata->name])) {
            $propertyMetadata->type = $this->translate(gettype($defaultProperties[$propertyMetadata->name]));
            return;
        }

        // 4. guess type from property's doc block
        if (null !== $type = $this->readTypeFromPropertyDocBlock($propertyMetadata->reflection)) {
            $propertyMetadata->type = $this->translate($type);
            return;
        }

        // defaults to 'string'
        $propertyMetadata->type =  'string';
    }

    private function readTypeFromGetter(\ReflectionMethod $getter): ?string
    {
        // try to read from its return type
        if (null !== $returnType = $getter->getReturnType()) {
            $type = $returnType->getName();
            if ($returnType->isBuiltin()) {
                $type = $this->translate($type);
            }
            return $type;
        }

        // try to read from its doc-block return type
        if (null !== $returnType = $this->readTypeFromGetterDocBlock($getter)) {
            return $this->translate($returnType);
        }

        return null;
    }

    private function readTypeFromSetter(\ReflectionMethod $setter): ?string
    {
        $args = $setter->getParameters();

        // 1. guess type from the first argument type hint
        if (count($args)) {
            /** @var \ReflectionParameter $firstArg */
            $firstArg = $args[0];
            if ($firstArg->hasType()) {
                return $firstArg->getType()->getName();
            }
        }
    }

    private function readTypeFromPropertyDocBlock(\ReflectionProperty $property): ?string
    {
        $docBlock = $property->getDocComment();

        if (is_string($docBlock)) {
            return $this->readFromDocComment($docBlock);
        }

        return null;
    }

    private function readTypeFromGetterDocBlock(\ReflectionMethod $getter): ?string
    {
        $docBlock = $getter->getDocComment();

        if (is_string($docBlock)) {
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
