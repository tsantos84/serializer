<?php

namespace TSantos\Serializer;

use Metadata\ClassMetadata;
use Metadata\MetadataFactoryInterface;

/**
 * Class Serializer
 *
 * @package Serializer
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class Serializer implements SerializerInterface
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
     * @inheritdoc
     */
    public function serialize($data, string $format, SerializationContext $context = null) : string
    {
        $encoder = $this->encoderRegistry->get($format);
        return $encoder->encode($this->toArray($data, $context));
    }

    /**
     * @inheritdoc
     */
    public function toArray($data, SerializationContext $context = null): array
    {
        if (is_scalar($data)) {
            return [$data];
        }

        if (null === $context) {
            $context = new SerializationContext();
        }

        if (!$context->isStarted()) {
            $context->start();
        }

        if ($context->isMaxDeepAchieve()) {
            return [];
        }

        if (is_array($data) || $data instanceof \Iterator) {
            return $this->collectionToArray($data, $context);
        }

        if ($context->hasObjectProcessed($data)) {
            return [];
        }

        $context->enter($data);

        $hierarchyMetadata = $this->metadataFactory->getMetadataForClass(get_class($data));
        $classMetadata = $hierarchyMetadata->getOutsideClassMetadata();
        $objectSerializer = $this->getObjectSerializer($classMetadata);
        $array = $objectSerializer->serialize($data, $context);

        $context->release();

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

    private function collectionToArray($data, SerializationContext $context): array
    {
        $context->enter();
        $array = [];
        foreach ($data as $key => $value) {
            if (is_scalar($value)) {
                $array[$key] = $value;
                continue;
            }
            $array[$key] = $this->toArray($value, $context);
        }
        $context->release();

        return $array;
    }
}
