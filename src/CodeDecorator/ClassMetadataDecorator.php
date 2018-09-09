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
use TSantos\Serializer\ClassMetadataAwareInterface;
use TSantos\Serializer\CodeDecoratorInterface;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\Traits\ClassMetadataAwareTrait;

/**
 * Class ReflectionPropertyMethodDecorator.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class ClassMetadataDecorator implements CodeDecoratorInterface
{
    public function decorate(PhpFile $file, PhpNamespace $namespace, ClassType $class, ClassMetadata $classMetadata): void
    {
        $needsClassMetadata = false;

        foreach ($classMetadata->propertyMetadata as $property) {
            if (null === $property->getter || null === $property->setter) {
                $needsClassMetadata = true;
                break;
            }
        }

        $needsClassMetadata = $classMetadata->reflection->getConstructor() || $needsClassMetadata;

        if (!$needsClassMetadata) {
            return;
        }

        $class
            ->addTrait(ClassMetadataAwareTrait::class)
            ->addImplement(ClassMetadataAwareInterface::class);
    }
}
