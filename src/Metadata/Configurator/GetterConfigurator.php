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

namespace TSantos\Serializer\Metadata\Configurator;

use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\Metadata\ConfiguratorInterface;
use TSantos\Serializer\Metadata\PropertyMetadata;

/**
 * Class ReadValueConfigurator.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class GetterConfigurator implements ConfiguratorInterface
{
    public function configure(ClassMetadata $classMetadata): void
    {
        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {
            if (null !== $propertyMetadata->getter) {
                continue;
            }

            $this->doConfigure($classMetadata, $propertyMetadata);
        }
    }

    private function doConfigure(ClassMetadata $classMetadata, PropertyMetadata $propertyMetadata): void
    {
        $ucName = ucfirst($propertyMetadata->name);
        $getters = ['get'.$ucName, 'is'.$ucName, 'has'.$ucName];
        $hasGetter = false;

        foreach ($getters as $getter) {
            if ($classMetadata->reflection->hasMethod($getter)) {
                $propertyMetadata->setGetter($getter);
                $hasGetter = true;
                break;
            }
        }

        if (!$hasGetter && $propertyMetadata->reflection->isPublic()) {
            $propertyMetadata->getter = $propertyMetadata->name;
        }
    }
}
