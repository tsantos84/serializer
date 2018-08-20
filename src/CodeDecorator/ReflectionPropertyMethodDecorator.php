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
 * Class ReflectionPropertyMethodDecorator.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class ReflectionPropertyMethodDecorator implements CodeDecoratorInterface
{
    public function decorate(PhpFile $file, PhpNamespace $namespace, ClassType $class, ClassMetadata $classMetadata): void
    {
        $hierarchy = [];

        $ref = $classMetadata->reflection;
        do {
            $hierarchy[] = $ref->getName();
        } while ($ref = $ref->getParentClass());

        $this->addReflectionPropertyMethod($class, $hierarchy);
    }

    private function addReflectionPropertyMethod(ClassType $class, $hierarchy): void
    {
        $classes = \implode(',', \array_map(function (string $class): string {
            return \sprintf("'%s' => new \ReflectionClass('%s')\n", $class, $class);
        }, $hierarchy));

        $method = $class->addMethod('getReflectionProperty')
            ->setVisibility('private')
            ->setReturnType('\ReflectionProperty')
            ->setBody(<<<STRING
static \$reflections;

if (null === \$reflections) {
    \$reflections = [
        $classes
    ];
}

return \$reflections[\$class]->getProperty(\$property);
STRING
            );

        $method
            ->addParameter('class')
            ->setTypeHint('string');

        $method
            ->addParameter('property')
            ->setTypeHint('string');
    }
}
