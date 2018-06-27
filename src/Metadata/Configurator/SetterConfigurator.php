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
 * Class SetterConfigurator.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class SetterConfigurator implements ConfiguratorInterface
{
    public function configure(ClassMetadata $classMetadata): void
    {
        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {
            if (null !== $propertyMetadata->setter) {
                continue;
            }

            $this->doConfigure($classMetadata, $propertyMetadata);
        }
    }

    private function doConfigure(ClassMetadata $classMetadata, PropertyMetadata $propertyMetadata): void
    {
        $ucName = ucfirst($propertyMetadata->name);

        if ($classMetadata->reflection->hasMethod($setter = 'set'.$ucName)) {
            $propertyMetadata->setSetter($setter);

            return;
        }

        if ($propertyMetadata->reflection->isPublic()) {
            $propertyMetadata->setter = $propertyMetadata->name;
        }
    }
}
