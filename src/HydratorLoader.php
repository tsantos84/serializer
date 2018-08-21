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
    const AUTOGENERATE_NEVER = 1;
    const AUTOGENERATE_ALWAYS = 2;
    const AUTOGENERATE_FILE_NOT_EXISTS = 3;

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
     * @var int
     */
    private $autogenerate;

    /**
     * @var HydratorCodeGenerator
     */
    private $codeGenerator;

    /**
     * @var HydratorCodeWriter
     */
    private $writer;

    /**
     * @var ObjectInstantiatorInterface
     */
    private $instantiator;

    /**
     * SerializerClassLoader constructor.
     *
     * @param Configuration $configuration
     * @param MetadataFactoryInterface $metadataFactory
     * @param HydratorCodeGenerator $codeGenerator
     * @param HydratorCodeWriter $writer
     * @param int $autogenerate
     * @param ObjectInstantiatorInterface $instantiator
     */
    public function __construct(
        Configuration $configuration,
        MetadataFactoryInterface $metadataFactory,
        HydratorCodeGenerator $codeGenerator,
        HydratorCodeWriter $writer,
        int $autogenerate,
        ObjectInstantiatorInterface $instantiator
    ) {
        $this->configuration = $configuration;
        $this->metadataFactory = $metadataFactory;
        $this->codeGenerator = $codeGenerator;
        $this->writer = $writer;
        $this->autogenerate = $autogenerate;
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

        $filename = $this->configuration->getFilename($classMetadata);

        switch ($this->autogenerate) {
            case self::AUTOGENERATE_NEVER:
                requireHydrator($filename);
                break;

            case self::AUTOGENERATE_ALWAYS:
                $this->generate($classMetadata);
                requireHydrator($filename);
                break;

            case self::AUTOGENERATE_FILE_NOT_EXISTS:
                if (!\file_exists($filename)) {
                    $this->generate($classMetadata);
                }
                requireHydrator($filename);
                break;
        }

        return $this->hydrators[$class] = $this->inject(new $fqn($this->instantiator), $serializer);
    }

    private function generate(ClassMetadata $classMetadata)
    {
        $code = $this->codeGenerator->generate($classMetadata);
        $this->writer->write($classMetadata, $code);
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

function requireHydrator(string $filename): void
{
    /** @noinspection PhpIncludeInspection */
    require_once $filename;
}
