<?php

namespace TSantos\Serializer;

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
     * @var EncoderRegistryInterface
     */
    private $encoders;

    /**
     * @var NormalizerRegistryInterface[]
     */
    private $normalizers;

    /**
     * Serializer constructor.
     * @param MetadataFactoryInterface $metadataFactory
     * @param SerializerClassGenerator $classGenerator
     * @param EncoderRegistryInterface $encoders
     * @param NormalizerRegistryInterface $normalizers
     */
    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        SerializerClassGenerator $classGenerator,
        EncoderRegistryInterface $encoders,
        NormalizerRegistryInterface $normalizers
    ) {
        $this->serializerClassGenerator = $classGenerator;
        $this->metadataFactory = $metadataFactory;
        $this->encoders = $encoders;
        $this->normalizers = $normalizers;
    }

    /**
     * @inheritdoc
     */
    public function serialize($data, string $format, SerializationContext $context = null) : string
    {
        $encoder = $this->encoders->get($format);

        if (is_null($data) || is_scalar($data)) {
            return $this->normalize($data, $context);
        }

        return $encoder->encode($this->normalize($data, $context));
    }

    /**
     * @inheritdoc
     */
    public function normalize($data, SerializationContext $context = null)
    {
        if (is_null($data) || is_scalar($data)) {
            return $data;
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

        if (null !== $normalizer = $this->normalizers->get($data, $context)) {
            return $normalizer->normalize($data, $context);
        }

        if (is_array($data) || $data instanceof \Iterator) {
            return $this->collectionToArray($data, $context);
        }

        if ($context->hasObjectProcessed($data)) {
            return [];
        }

        return $this->serializeObject($data, $context);
    }

    private function serializeObject($object, SerializationContext $context): array
    {
        if ($object instanceof \JsonSerializable) {
            return $this->normalize($object->jsonSerialize(), $context);
        }

        $context->enter($object);
        $classMetadata = $this->metadataFactory->getMetadataForClass(get_class($object));
        $objectSerializer = $this->serializerClassGenerator->getGeneratorFor($classMetadata, $this);
        $array = $objectSerializer->serialize($object, $context);
        $context->left();

        return $array;
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
            $array[$key] = $this->normalize($value, $context);
        }
        $context->left();

        return $array;
    }
}
