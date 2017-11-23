<?php

namespace TSantos\Serializer;

use Metadata\Cache\CacheInterface;
use Metadata\Driver\DriverChain;
use Metadata\Driver\DriverInterface;
use Metadata\Driver\FileLocator;
use Metadata\MetadataFactory;
use TSantos\Serializer\Encoder\JsonEncoder;
use TSantos\Serializer\Metadata\Driver\PhpDriver;
use TSantos\Serializer\Metadata\Driver\XmlDriver;
use TSantos\Serializer\Metadata\Driver\YamlDriver;
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
    private $metadataDirs;
    private $serializerClassGenerateStrategy;

    /**
     * Builder constructor.
     */
    public function __construct()
    {
        $this->encoders = new EncoderRegistry();
        $this->normalizers = new NormalizerRegistry();
        $this->debug = false;
        $this->metadataDirs = [];
        $this->serializerClassGenerateStrategy = SerializerClassLoader::AUTOGENERATE_ALWAYS;
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

    public function setMetadataDirs(array $dirs): SerializerBuilder
    {
        $this->metadataDirs = [];
        $this->addMetadataDirs($dirs);
        return $this;
    }

    public function addMetadataDirs(array $dirs): SerializerBuilder
    {
        foreach ($dirs as $namespace => $dir) {
            $this->addMetadataDir($namespace, $dir);
        }

        return $this;
    }

    public function addMetadataDir(string $namespace, string $dir): SerializerBuilder
    {
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException('The metadata directory "' . $dir . '" does not exist');
        }

        $this->metadataDirs[$namespace] = $dir;
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

    public function addDefaultNormalizers(): SerializerBuilder
    {
        $this->normalizers->add(new DateTimeNormalizer());
        $this->normalizers->add(new IdentityNormalizer());
        return $this;
    }

    public function setMetadataCache(CacheInterface $cache): SerializerBuilder
    {
        $this->cache = $cache;
        return $this;
    }

    public function setSerializerClassGenerateStrategy(int $serializerClassGenerateStrategy): SerializerBuilder
    {
        $this->serializerClassGenerateStrategy = $serializerClassGenerateStrategy;
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
            $fileLocator = new FileLocator($this->metadataDirs);
            $typeGuesser = new TypeGuesser();
            $driver = new DriverChain([
                new YamlDriver($fileLocator, $typeGuesser),
                new XmlDriver($fileLocator, $typeGuesser),
                new PhpDriver($fileLocator, $typeGuesser)
            ]);
        }

        $metadataFactory = new MetadataFactory($driver, 'Metadata\ClassHierarchyMetadata', $this->debug);
        if (null !== $this->cache) {
            $metadataFactory->setCache($this->cache);
        }

        $classLoader = new SerializerClassLoader(
            $metadataFactory,
            new SerializerClassCodeGenerator(),
            new SerializerClassWriter($classDir),
            $this->serializerClassGenerateStrategy
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
