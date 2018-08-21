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

use Metadata\MetadataFactoryInterface;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\ObjectInstantiator\ObjectInstantiatorInterface;

/**
 * Class HydratorLoader.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class HydratorLoader
{
    const AUTOGENERATE_NEVER = Compiler::AUTOGENERATE_NEVER;
    const AUTOGENERATE_ALWAYS = Compiler::AUTOGENERATE_ALWAYS;
    const AUTOGENERATE_FILE_NOT_EXISTS = Compiler::AUTOGENERATE_FILE_NOT_EXISTS;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var array
     */
    private $hydrators = [];

    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var ObjectInstantiatorInterface
     */
    private $instantiator;

    /**
     * @var Compiler
     */
    private $compiler;

    /**
     * HydratorLoader constructor.
     * @param Configuration $configuration
     * @param MetadataFactoryInterface $metadataFactory
     * @param Compiler $compiler
     * @param ObjectInstantiatorInterface $instantiator
     */
    public function __construct(
        Configuration $configuration,
        MetadataFactoryInterface $metadataFactory,
        Compiler $compiler,
        ObjectInstantiatorInterface $instantiator
    ) {
        $this->configuration = $configuration;
        $this->metadataFactory = $metadataFactory;
        $this->compiler = $compiler;
        $this->instantiator = $instantiator;
    }

    /**
     * @param string              $class
     * @param SerializerInterface $serializer
     *
     * @return HydratorInterface
     */
    public function load(string $class, SerializerInterface $serializer): HydratorInterface
    {
        if (isset($this->hydrators[$class])) {
            return $this->hydrators[$class];
        }

        /** @var ClassMetadata $classMetadata */
        $classMetadata = $this->metadataFactory->getMetadataForClass($class);

        if (null === $classMetadata) {
            throw new \RuntimeException(
                'No mapping file was found for class '.$class.
                '. Did you configure the correct paths for serializer?'
            );
        }

        $fqn = $this->configuration->getFQNClassName($classMetadata);

        if (\class_exists($fqn, false)) {
            return $this->hydrators[$class] = $this->inject(new $fqn($this->instantiator), $serializer);
        }

        $this->compiler->compile($classMetadata);

        return $this->hydrators[$class] = $this->inject(new $fqn($this->instantiator), $serializer);
    }

    private function inject(HydratorInterface $hydrator, SerializerInterface $serializer): HydratorInterface
    {
        if ($hydrator instanceof SerializerAwareInterface) {
            $hydrator->setSerializer($serializer);
        }

        if ($hydrator instanceof HydratorLoaderAwareInterface) {
            $hydrator->setLoader($this);
        }

        return $hydrator;
    }
}
