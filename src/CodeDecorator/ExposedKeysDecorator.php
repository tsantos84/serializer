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

namespace TSantos\Serializer\CodeDecorator;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use TSantos\Serializer\AbstractContext;
use TSantos\Serializer\CodeDecoratorInterface;
use TSantos\Serializer\Metadata\ClassMetadata;

/**
 * Class HelperMethodsDecorator.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class ExposedKeysDecorator implements CodeDecoratorInterface
{
    public function decorate(PhpFile $file, PhpNamespace $namespace, ClassType $class, ClassMetadata $classMetadata): void
    {
        $class
            ->addProperty('exposedGroups')
            ->setVisibility('private')
            ->setStatic(true)
            ->setValue($this->getGroups($classMetadata));

        $method = $class->addMethod('getExposedKeys')
            ->setVisibility('private')
            ->setReturnType('array')
            ->setStatic(true)
            ->setBody(<<<STRING
\$exposedKeys = [];
\$contextGroups = \$context->getGroups();

foreach (\$contextGroups as \$group => \$val) {
    if (isset(static::\$exposedGroups[\$group])) {
        \$exposedKeys = \array_merge(\$exposedKeys, static::\$exposedGroups[\$group]);
    }
}

return \$exposedKeys;
STRING
            );

        $method
            ->addParameter('context')
            ->setTypeHint(AbstractContext::class);
    }

    private function getGroups(ClassMetadata $metadata): array
    {
        $groups = [];
        foreach ($metadata->propertyMetadata as $property) {
            foreach ($property->groups as $group) {
                $groups[$group][$property->exposeAs] = true;
            }
        }

        foreach ($metadata->methodMetadata as $method) {
            foreach ($method->groups as $group) {
                $groups[$group][$method->exposeAs] = true;
            }
        }

        return $groups;
    }
}
