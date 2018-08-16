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

namespace TSantos\Serializer;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use TSantos\Serializer\Metadata\ClassMetadata;

/**
 * Class CodeDecoratorInterface.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
interface CodeDecoratorInterface
{
    /**
     * Decorates the hydrator class.
     *
     * It can be used to add custom logic into your hydrator class or modify the existing ones.
     *
     * @param PhpFile       $file
     * @param PhpNamespace  $namespace
     * @param ClassType     $class
     * @param ClassMetadata $classMetadata
     */
    public function decorate(PhpFile $file, PhpNamespace $namespace, ClassType $class, ClassMetadata $classMetadata): void;
}
