<?php

namespace TSantos\Serializer;

use Metadata\Cache\CacheInterface;
use Metadata\Driver\DriverInterface;
use Metadata\MetadataFactory;
use TSantos\Serializer\Encoder\JsonEncoder;
use TSantos\Serializer\Metadata\Driver\InMemoryDriver;
use TSantos\Serializer\Normalizer\DateTimeNormalizer;
use TSantos\Serializer\Normalizer\IdentityNormalizer;

/**
 * Class Builder
 *
 * @package Serializer
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class SerializerBuilder
{
    private $encoders;
    private $normalizers;
    private $driver;
    private $cache;
    private $debug;
    private $serializerClassDir;

    /**
     * Builder constructor.
     */
    public function __construct()
    {
        $this->encoders = new EncoderRegistry();
        $this->normalizers = new NormalizerRegistry();
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

    public function setSerializerClassDir(string $dir): SerializerBuilder
    {
        if (!is_dir($dir)) {
            $this->createDir($dir);
        }

        if (!is_writable($dir)) {
            throw new \InvalidArgumentException(sprintf('The serializer class directory "%s" is not writable.', $dir));
        }

        $this->serializerClassDir = $dir;

        return $this;
    }

    public function setDebug(bool $debug): SerializerBuilder
    {
        $this->debug = $debug;
        return $this;
    }

    public function addDefaultNormalizers()
    {
        $this->normalizers->add(new DateTimeNormalizer());
        $this->normalizers->add(new IdentityNormalizer());
        return $this;
    }

    public function setMetadataCache(CacheInterface $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * @return SerializerInterface
     */
    public function build(): SerializerInterface
    {
        $this->encoders->add(new JsonEncoder());

        if (null === $classDir = $this->serializerClassDir) {
            $this->createDir($classDir = sys_get_temp_dir() . '/serializer/classes');
        }

        if (null === $driver = $this->driver) {
            $driver = new InMemoryDriver([], new TypeGuesser());
        }

        $metadataFactory = new MetadataFactory($driver, 'Metadata\ClassHierarchyMetadata', $this->debug);
        if (null !== $this->cache) {
            $metadataFactory->setCache($this->cache);
        }

        $classLoader = new SerializerClassLoader(
            $metadataFactory,
            new SerializerClassCodeGenerator(),
            new SerializerClassWriter($classDir),
            SerializerClassLoader::AUTOGENERATE_ALWAYS
        );

        $serializer = new Serializer(
            $classLoader,
            $this->encoders,
            $this->normalizers
        );

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
