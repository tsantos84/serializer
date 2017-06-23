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
     * @param SerializationContext $context
     * @return string
     */
    public function serialize($object, string $format, SerializationContext $context = null) : string
    {
        $encoder = $this->encoderRegistry->get($format);
        return $encoder->encode($this->toArray($object, $context));
    }

    public function toArray($object, SerializationContext $context = null): array
    {
        if (null === $context) {
            $context = new SerializationContext();
        }

        if (is_array($object) || $object instanceof \Iterator) {
            $array = [];
            foreach ($object as $key => $item) {
                $array[$key] = $this->toArray($item, $context);
            }
            return $array;
        }

        $hierarchyMetadata = $this->metadataFactory->getMetadataForClass(get_class($object));
        $classMetadata = $hierarchyMetadata->getOutsideClassMetadata();

        $objectSerializer = $this->getObjectSerializer($classMetadata, $context);

        $array = $objectSerializer->serialize($object, $context);

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
