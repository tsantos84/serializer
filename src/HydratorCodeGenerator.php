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

namespace TSantos\Serializer;

use Nette\PhpGenerator\Helpers;
use Nette\PhpGenerator\PhpFile;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\Traits\HydratorLoaderAwareTrait;
use TSantos\Serializer\Traits\ObjectInstantiatorAwareTrait;
use TSantos\Serializer\Traits\SerializerAwareTrait;

/**
 * Class HydratorCodeGenerator.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class HydratorCodeGenerator
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var CodeDecoratorInterface[]
     */
    private $decorators = [];

    /**
     * ChainDecorator constructor.
     *
     * @param Configuration            $configuration
     * @param CodeDecoratorInterface[] $decorators
     */
    public function __construct(Configuration $configuration, array $decorators = [])
    {
        $this->configuration = $configuration;
        $this->decorators = $decorators;
    }

    public function addDecorator(CodeDecoratorInterface $decorator): self
    {
        $this->decorators[] = $decorator;

        return $this;
    }

    /**
     * @param ClassMetadata $classMetadata
     *
     * @return string
     */
    public function generate(ClassMetadata $classMetadata): string
    {
        $phpFile = new PhpFile();

        $namespace = $phpFile->addNamespace($this->configuration->getNamespaceForClass($classMetadata));

        $class = $namespace
            ->addClass($this->configuration->getClassName($classMetadata))
            ->setComment('THIS CLASS WAS GENERATED BY THE SERIALIZER. DO NOT EDIT THIS FILE.')
            ->setFinal(true)
            ->setImplements([HydratorInterface::class, SerializerAwareInterface::class, ObjectInstantiatorAwareInterface::class]);

        if (null !== $classMetadata->baseClass) {
            $class->addExtend($classMetadata->baseClass);
        }

        $class->addTrait(SerializerAwareTrait::class);
        $class->addTrait(ObjectInstantiatorAwareTrait::class);

        if ($classMetadata->isAbstract()) {
            $class->addTrait(HydratorLoaderAwareTrait::class);
        }

        foreach ($this->decorators as $decorator) {
            $decorator->decorate($phpFile, $namespace, $class, $classMetadata);
        }

        return Helpers::tabsToSpaces((string) $phpFile, 4);
    }
}
