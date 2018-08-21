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

/**
 * Class ConstructorMethodDecorator.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class ConstructorMethodDecorator implements CodeDecoratorInterface
{
    public function decorate(PhpFile $file, PhpNamespace $namespace, ClassType $class, ClassMetadata $classMetadata): void
    {
        if (empty($classMetadata->hydratorConstructArgs)) {
            return;
        }

        $constructor = $class->addMethod('__construct');

        foreach ($classMetadata->hydratorConstructArgs as $argName => $arg) {
            $param = $constructor->addParameter($argName);

            if (isset($arg['type'])) {
                $param->setTypeHint($arg['type']);
            }

            $constructor
                ->addComment(\sprintf('@param %s $%s', $arg['type'] ?? '', $argName))
                ->addBody(\sprintf('$this->%s = $%s;', $argName, $argName));

            $class
                ->addProperty($argName)
                ->setVisibility('private')
                ->setComment('@var '.$arg['type'] ?? '');
        }
    }
}
