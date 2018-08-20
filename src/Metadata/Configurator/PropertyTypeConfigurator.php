<?php

declare(strict_types=1);

/*
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Serializer\Metadata\Configurator;

use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\PropertyInfo\Type;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\Metadata\ConfiguratorInterface;

/**
 * Class PropertyTypeConfigurator.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class PropertyTypeConfigurator implements ConfiguratorInterface
{
    /**
     * @var PropertyInfoExtractorInterface
     */
    private $propertyInfo;

    public function __construct(PropertyInfoExtractorInterface $propertyInfo)
    {
        $this->propertyInfo = $propertyInfo;
    }

    public function configure(ClassMetadata $classMetadata): void
    {
        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {
            if (null !== $propertyMetadata->type) {
                continue;
            }

            $types = $this->propertyInfo->getTypes($classMetadata->name, $propertyMetadata->name);

            if (null === $types) {
                $propertyMetadata->type = 'string';
                continue;
            }

            /** @var Type $propertyType */
            $propertyType = \current($types);
            $propertyMetadata->type = $this->extract($propertyType);
        }
    }

    private function extract(Type $type): string
    {
        if (!$type->isCollection()) {
            return $this->phpBuiltInOrClass($type);
        }

        if (null !== $innerType = $type->getCollectionValueType()) {
            return $this->phpBuiltInOrClass($innerType).'[]';
        }

        return 'mixed[]';
    }

    private function phpBuiltInOrClass(Type $type): string
    {
        return Type::BUILTIN_TYPE_OBJECT === $type->getBuiltinType()
            ? $type->getClassName()
            : $this->translate($type->getBuiltinType());
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
