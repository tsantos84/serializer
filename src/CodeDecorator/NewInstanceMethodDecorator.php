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
use TSantos\Serializer\DeserializationContext;
use TSantos\Serializer\Metadata\ClassMetadata;

/**
 * Class CreateInstanceMethodDecorator.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class NewInstanceMethodDecorator implements CodeDecoratorInterface
{
    public function decorate(PhpFile $file, PhpNamespace $namespace, ClassType $class, ClassMetadata $classMetadata): void
    {
        $method = $class
            ->addMethod('newInstance')
            ->addComment('@param array $data')
            ->addComment('@param '.DeserializationContext::class.' $context')
            ->addComment('@return '.$classMetadata->name)
        ;

        $method
            ->addParameter('data')
            ->setTypeHint('array');

        $method
            ->addParameter('context')
            ->setTypeHint(DeserializationContext::class);

        $method->setBody(<<<STRING
return new {$classMetadata->name}();
STRING
);
    }
}
