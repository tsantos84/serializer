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
        return $encoder->encode($this->toArray($data, $context));
    }

    /**
     * @inheritdoc
     */
    public function toArray($data, SerializationContext $context = null)
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

        if (null !== $normalizer = $this->normalizers->get($data, $context)) {
            return $normalizer->normalize($data, $context);
        }

        if (is_array($data) || $data instanceof \Iterator) {
            return $this->collectionToArray($data, $context);
        }

        if ($context->hasObjectProcessed($data)) {
            return [];
        }

        $context->enter($data);

        $classMetadata = $this->metadataFactory->getMetadataForClass(get_class($data));
        $objectSerializer = $this->serializerClassGenerator->getGeneratorFor($classMetadata, $this);
        $array = $objectSerializer->serialize($data, $context);

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
            $array[$key] = $this->toArray($value, $context);
        }
        $context->left();

        return $array;
    }
}
