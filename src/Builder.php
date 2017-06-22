<?php

namespace Serializer;

use Metadata\Driver\DriverInterface;
use Metadata\MetadataFactory;
use Serializer\Encoder\JsonEncoder;
use Serializer\Type\DateTimeType;
use Serializer\Type\FloatType;
use Serializer\Type\IntegerType;
use Serializer\Type\StringType;

/**
 * Class Builder
 *
 * @package Serializer
 * @author Tales Santos <tales.maxmilhas@gmail.com>
 */
class Builder
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
     * @return Builder
     */
    public function setMetadataDriver(DriverInterface $driver): Builder
    {
        $this->driver = $driver;
        return $this;
    }

    public function setSerializerClassGenerator(SerializerClassGenerator $generator): Builder
    {
        $this->serializerClassGenerator = $generator;
        return $this;
    }

    public function setCacheDir(string $dir): Builder
    {
        $this->cache = $dir;
        return $this;
    }

    public function setDebug(bool $debug): Builder
    {
        $this->debug = $debug;
        return $this;
    }

    /**
     * @return Serializer
     */
    public function build(): Serializer
    {
        $this->typeRegistry
            ->addType(new IntegerType())
            ->addType(new FloatType())
            ->addType(new StringType());

        $this->encoderRegistry->add(new JsonEncoder());

        $metadataFactory = new MetadataFactory($this->driver, 'Metadata\ClassHierarchyMetadata', $this->debug);

        if (null === $this->serializerClassGenerator) {
            $this->serializerClassGenerator = new SerializerClassGenerator($this->cache ?? sys_get_temp_dir(), $this->typeRegistry, $this->debug);
        }

        $serializer = new Serializer($metadataFactory, $this->serializerClassGenerator, $this->encoderRegistry);

        return $serializer;
    }
}
