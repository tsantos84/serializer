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
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\ObjectInstantiator\ObjectInstantiatorInterface;

/**
 * Class ConstructorMethodDecorator.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class ConstructorMethodDecorator implements CodeDecoratorInterface
{
    public function decorate(PhpFile $file, PhpNamespace $namespace, ClassType $class, ClassMetadata $classMetadata): void
    {
        $constructor = $class
            ->addMethod('__construct')
            ->addComment('@param '.ObjectInstantiatorInterface::class.' $instantiator')
        ;

        $constructor
            ->addParameter('instantiator')
            ->setTypeHint(ObjectInstantiatorInterface::class);

        $constructor
            ->setBody(<<<STRING
\$this->instantiator = \$instantiator;
STRING
);
    }
}
