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
use TSantos\Serializer\CodeDecoratorInterface;
use TSantos\Serializer\HydratorLoaderAwareInterface;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\Traits\HydratorLoaderAwareTrait;

/**
 * Class AbstractHydratorDecorator.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class AbstractHydratorDecorator implements CodeDecoratorInterface
{
    public function decorate(PhpFile $file, PhpNamespace $namespace, ClassType $class, ClassMetadata $classMetadata): void
    {
        if (!$classMetadata->isAbstract()) {
            return;
        }

        $class
            ->addProperty('discriminatorMapping')
            ->setVisibility('private')
            ->setStatic(true)
            ->setValue(\array_flip($classMetadata->discriminatorMapping));

        $class
            ->addImplement(HydratorLoaderAwareInterface::class);

        $class->addTrait(HydratorLoaderAwareTrait::class);
    }
}
