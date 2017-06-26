<?php

namespace TSantos\Serializer;

use Metadata\Driver\DriverInterface;
use Metadata\MetadataFactory;
use TSantos\Serializer\Encoder\JsonEncoder;

/**
 * Class Builder
 *
 * @package Serializer
 * @author Tales Santos <tales.maxmilhas@gmail.com>
 */
class SerializerBuilder
{
    private $typeRegistry;
    private $encoderRegistry;
    private $driver;
    private $serializerClassGenerator;
    private $cache;
    private $debug;

    /**
     * Builder constructor.
     */
    public function __construct()
    {
        $this->typeRegistry = new TypeRegistry();
        $this->encoderRegistry = new EncoderRegistry();
        $this->debug = false;
    }

    /**
     * @param DriverInterface $driver
     * @return SerializerBuilder
     */
    public function setMetadataDriver(DriverInterface $driver): SerializerBuilder
    {
        $this->driver = $driver;
        return $this;
    }

    public function setSerializerClassGenerator(SerializerClassGenerator $generator): SerializerBuilder
    {
        $this->serializerClassGenerator = $generator;
        return $this;
    }

    public function setCacheDir(string $dir): SerializerBuilder
    {
        if (!is_dir($dir)) {
            $this->createDir($dir);
        }

        if (!is_writable($dir)) {
            throw new \InvalidArgumentException(sprintf('The cache directory "%s" is not writable.', $dir));
        }

        $this->cache = $dir;
        return $this;
    }

    public function setDebug(bool $debug): SerializerBuilder
    {
        $this->debug = $debug;
        return $this;
    }

    /**
     * @return SerializerInterface
     */
    public function build(): SerializerInterface
    {
        $this->encoderRegistry->add(new JsonEncoder());

        $metadataFactory = new MetadataFactory($this->driver, 'Metadata\ClassHierarchyMetadata', $this->debug);

        if (null === $this->serializerClassGenerator) {
            $this->serializerClassGenerator = new SerializerClassGenerator($this->cache ?? sys_get_temp_dir(), $this->typeRegistry, $this->debug);
        }

        $serializer = new Serializer($metadataFactory, $this->serializerClassGenerator, $this->encoderRegistry);

        return $serializer;
    }

    private function createDir($dir)
    {
        if (is_dir($dir)) {
            return;
        }
        if (false === @mkdir($dir, 0777, true) && false === is_dir($dir)) {
            throw new \RuntimeException(sprintf('Could not create directory "%s".', $dir));
        }
    }
}
