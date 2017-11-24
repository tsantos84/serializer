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

use Metadata\ClassMetadata;
use Metadata\MetadataFactoryInterface;

/**
 * Class SerializerClassLoader
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class SerializerClassLoader
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
     * @var SerializerClassCodeGenerator
     */
    private $codeGenerator;

    /**
     * @var SerializerClassWriter
     */
    private $writer;

    /**
     * SerializerClassLoader constructor.
     * @param MetadataFactoryInterface $metadataFactory
     * @param SerializerClassCodeGenerator $codeGenerator
     * @param SerializerClassWriter $writer
     * @param int $autogenerate
     */
    public function __construct(MetadataFactoryInterface $metadataFactory, SerializerClassCodeGenerator $codeGenerator, SerializerClassWriter $writer, int $autogenerate)
    {
        $this->metadataFactory = $metadataFactory;
        $this->codeGenerator = $codeGenerator;
        $this->writer = $writer;
        $this->autogenerate = $autogenerate;
    }

    /**
     * @param object $object
     * @return SerializerClassInterface
     */
    public function load($object, SerializerInterface $serializer): SerializerClassInterface
    {
        $classMetadata = $this->metadataFactory->getMetadataForClass(get_class($object));

        if (null === $classMetadata) {
            throw new \RuntimeException('No mapping file was found for class ' . get_class($object) . '. Did you configure the correct paths for serializer?');
        }

        $fqn = $this->getClassName($classMetadata);

        if (isset($this->instances[$fqn])) {
            return $this->instances[$fqn];
        }

        if (class_exists($fqn, false)) {
            return $this->instances[$fqn] = new $fqn($serializer, $classMetadata);
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

        return $this->instances[$fqn] = new $fqn($serializer, $classMetadata);
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
}
