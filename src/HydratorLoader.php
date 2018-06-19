<?php
/**
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

/**
 * Class HydratorLoader
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class HydratorLoader
{
    const AUTOGENERATE_NEVER = 1;
    const AUTOGENERATE_ALWAYS = 2;
    const AUTOGENERATE_FILE_NOT_EXISTS = 3;

    /**
     * @var array
     */
    private $instances = [];

    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var bool
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
     * SerializerClassLoader constructor.
     * @param MetadataFactoryInterface $metadataFactory
     * @param HydratorCodeGenerator $codeGenerator
     * @param HydratorCodeWriter $writer
     * @param int $autogenerate
     */
    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        HydratorCodeGenerator $codeGenerator,
        HydratorCodeWriter $writer,
        int $autogenerate
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->codeGenerator = $codeGenerator;
        $this->writer = $writer;
        $this->autogenerate = $autogenerate;
    }

    /**
     * @param string $class
     * @param SerializerInterface $serializer
     * @return HydratorInterface
     */
    public function load(string $class, SerializerInterface $serializer): HydratorInterface
    {
        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        }

        /** @var ClassMetadata $classMetadata */
        $classMetadata = $this->metadataFactory->getMetadataForClass($class);

        if (null === $classMetadata) {
            throw new \RuntimeException(
                'No mapping file was found for class ' . $class .
                '. Did you configure the correct paths for serializer?'
            );
        }

        $fqn = $this->getClassName($classMetadata);

        if (class_exists($fqn, false)) {
            return $this->instances[$class] = $this->injectSerializer(new $fqn(), $serializer);
        }

        $filename = $this->getFilename($classMetadata);

        switch ($this->autogenerate) {
            case self::AUTOGENERATE_NEVER:
                require_once $filename;
                break;

            case self::AUTOGENERATE_ALWAYS:
                $this->generate($classMetadata);
                require_once $filename;
                break;

            case self::AUTOGENERATE_FILE_NOT_EXISTS:
                if (!file_exists($filename)) {
                    $this->generate($classMetadata);
                }
                require_once $filename;
                break;
        }

        return $this->instances[$class] = $this->injectSerializer(new $fqn(), $serializer);
    }

    private function generate(ClassMetadata $classMetadata)
    {
        $code = $this->codeGenerator->generate($classMetadata);
        $this->writer->write($classMetadata, $code);
    }

    private function getClassName(ClassMetadata $classMetadata): string
    {
        return $this->codeGenerator->getClassName($classMetadata);
    }

    private function getFilename(ClassMetadata $classMetadata): string
    {
        return $this->writer->getPath() . DIRECTORY_SEPARATOR . $this->getClassName($classMetadata) . '.php';
    }

    private function injectSerializer(HydratorInterface $hydrator, SerializerInterface $serializer): HydratorInterface
    {
        if ($hydrator instanceof SerializerAwareInterface) {
            $hydrator->setSerializer($serializer);
        }

        return $hydrator;
    }
}