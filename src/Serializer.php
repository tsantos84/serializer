<?php

namespace Serializer;

use Metadata\ClassMetadata;
use Metadata\MetadataFactoryInterface;

/**
 * Class Serializer
 *
 * @package Serializer
 * @author Tales Santos <tales.maxmilhas@gmail.com>
 */
class Serializer
{
    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var SerializerClassGenerator
     */
    private $serializerClassGenerator;

    /**
     * @var EncoderRegistry
     */
    private $encoderRegistry;

    /**
     * Serializer constructor.
     * @param MetadataFactoryInterface $metadataFactory
     * @param SerializerClassGenerator $classGenerator
     * @param EncoderRegistry $encoderRegistry
     * @internal param DriverInterface $driver
     */
    public function __construct(MetadataFactoryInterface $metadataFactory, SerializerClassGenerator $classGenerator, EncoderRegistry $encoderRegistry)
    {
        $this->serializerClassGenerator = $classGenerator;
        $this->metadataFactory = $metadataFactory;
        $this->encoderRegistry = $encoderRegistry;
    }

    /**
     * @param $object
     * @param string $format
     * @return string
     */
    public function serialize($object, string $format)
    {
        $encoder = $this->encoderRegistry->get($format);

        $hierarchyMetadata = $this->metadataFactory->getMetadataForClass(get_class($object));
        $classMetadata = $hierarchyMetadata->getOutsideClassMetadata();

        $objectSerializer = $this->getObjectSerializer($classMetadata);

        $array = $objectSerializer->serialize($classMetadata, $object, $this);

        return $encoder->encode($array);
    }

    /**
     * @param ClassMetadata $metadata
     * @return SerializerClassInterface
     */
    private function getObjectSerializer(ClassMetadata $metadata): SerializerClassInterface
    {
        return $this->serializerClassGenerator->getGeneratorFor($metadata);
    }
}
