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
        return $encoder->encode($this->toArray($object));
    }

    public function toArray($object): array
    {
        if (is_array($object) || $object instanceof \Iterator) {
            $array = [];
            foreach ($object as $key => $item) {
                $array[$key] = $this->toArray($item);
            }
            return $array;
        }

        $hierarchyMetadata = $this->metadataFactory->getMetadataForClass(get_class($object));
        $classMetadata = $hierarchyMetadata->getOutsideClassMetadata();

        $objectSerializer = $this->getObjectSerializer($classMetadata);

        $array = $objectSerializer->serialize($object);

        return $array;
    }

    /**
     * @param ClassMetadata $metadata
     * @return SerializerClassInterface
     */
    private function getObjectSerializer(ClassMetadata $metadata): SerializerClassInterface
    {
        return $this->serializerClassGenerator->getGeneratorFor($metadata, $this);
    }
}
