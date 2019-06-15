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
use TSantos\Serializer\Exception\MappingException;
use TSantos\Serializer\Metadata\ClassMetadata;

/**
 * Class HydratorLoader.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class HydratorLoader implements HydratorLoaderInterface
{
    const COMPILE_NEVER = 1;
    const COMPILE_ALWAYS = 2;
    const COMPILE_IF_NOT_EXISTS = 3;

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
     * @var HydratorCompilerInterface
     */
    private $compiler;

    /**
     * @var HydratorFactoryInterface
     */
    private $factory;

    /**
     * HydratorLoader constructor.
     *
     * @param Configuration             $configuration
     * @param MetadataFactoryInterface  $metadataFactory
     * @param HydratorCompilerInterface $compiler
     * @param HydratorFactoryInterface  $factory
     */
    public function __construct(
        Configuration $configuration,
        MetadataFactoryInterface $metadataFactory,
        HydratorCompilerInterface $compiler,
        HydratorFactoryInterface $factory
    ) {
        $this->configuration = $configuration;
        $this->metadataFactory = $metadataFactory;
        $this->compiler = $compiler;
        $this->factory = $factory;
    }

    /**
     * @param string $class
     *
     * @return HydratorInterface
     */
    public function load(string $class): HydratorInterface
    {
        if (isset($this->hydrators[$class])) {
            return $this->hydrators[$class];
        }

        /** @var ClassMetadata $classMetadata */
        $classMetadata = $this->metadataFactory->getMetadataForClass($class);

        if (null === $classMetadata) {
            throw new MappingException(
                'No mapping file was found for class '.$class.
                '. Did you configure the correct paths for serializer?'
            );
        }

        $fqn = $this->configuration->getFQNClassName($classMetadata);

        if (\class_exists($fqn, false)) {
            return $this->hydrators[$class] = $this->factory->newInstance($classMetadata);
        }

        $this->compileAndLoad($classMetadata);

        return $this->hydrators[$class] = $this->factory->newInstance($classMetadata);
    }

    private function compileAndLoad(ClassMetadata $classMetadata): void
    {
        $filename = $this->configuration->getFilename($classMetadata);

        switch ($this->configuration->getGenerationStrategy()) {
            case self::COMPILE_NEVER:
                requireHydrator($filename);
                break;

            case self::COMPILE_ALWAYS:
                $this->compiler->compile($classMetadata);
                requireHydrator($filename);
                break;

            case self::COMPILE_IF_NOT_EXISTS:
                if (!\file_exists($filename)) {
                    $this->compiler->compile($classMetadata);
                }
                requireHydrator($filename);
                break;
        }
    }
}

function requireHydrator(string $filename): void
{
    /** @noinspection PhpIncludeInspection */
    require_once $filename;
}
