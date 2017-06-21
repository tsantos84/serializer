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
    private $driver;
    private $serializerClassGenerator;
    private $cache;

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

    /**
     * @return Serializer
     */
    public function build(): Serializer
    {
        $typeRegistry = new TypeRegistry();
        $typeRegistry
            ->addType(new IntegerType())
            ->addType(new FloatType())
            ->addType(new StringType());

        $encoderRegistry = new EncoderRegistry();
        $encoderRegistry->add(new JsonEncoder());

        $metadataFactory = new MetadataFactory($this->driver);

        if (null === $this->serializerClassGenerator) {
            $this->serializerClassGenerator = new SerializerClassGenerator($this->cache ?? sys_get_temp_dir(), $typeRegistry);
        }

        $serializer = new Serializer($metadataFactory, $this->serializerClassGenerator, $encoderRegistry);

        return $serializer;
    }
}
