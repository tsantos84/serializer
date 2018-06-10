<?php

namespace TSantos\Serializer\Normalizer;

use TSantos\Serializer\CacheableNormalizerInterface;
use TSantos\Serializer\DeserializationContext;
use TSantos\Serializer\ObjectInstantiator\ObjectInstantiatorInterface;
use TSantos\Serializer\SerializationContext;
use TSantos\Serializer\SerializerAwareInterface;
use TSantos\Serializer\SerializerClassLoader;
use TSantos\Serializer\Traits\SerializerAwareTrait;

/**
 * Class ObjectNormalizer
 * @package TSantos\Serializer\Normalizer
 */
class ObjectNormalizer implements
    NormalizerInterface,
    DenormalizerInterface,
    SerializerAwareInterface,
    CacheableNormalizerInterface
{
    use SerializerAwareTrait;

    /**
     * @var SerializerClassLoader
     */
    private $classLoader;

    /**
     * @var ObjectInstantiatorInterface
     */
    private $instantiator;

    /**
     * ObjectNormalizer constructor.
     * @param SerializerClassLoader $classLoader
     * @param ObjectInstantiatorInterface $instantiator
     */
    public function __construct(SerializerClassLoader $classLoader, ObjectInstantiatorInterface $instantiator)
    {
        $this->classLoader = $classLoader;
        $this->instantiator = $instantiator;
    }

    public function normalize($data, SerializationContext $context)
    {
        $objectSerializer = $this->classLoader->load(get_class($data), $this->serializer);

        $context->enter($data);
        $array = $objectSerializer->serialize($data, $context);
        $context->left();

        return $array;
    }

    public function canBeCachedByType(): bool
    {
        return true;
    }

    public function supportsNormalization($data, SerializationContext $context): bool
    {
        return is_object($data) && !is_iterable($data) && !$data instanceof \DateTimeInterface;
    }

    public function denormalize($data, string $type, DeserializationContext $context)
    {
        if (empty($data)) {
            return [];
        }

        if (null === $object = $context->getTarget()) {
            $object = $this->instantiator->create($type, $data, $context);
        }

        $objectSerializer = $this->classLoader->load($type, $this->serializer);
        $context->enter();
        $object = $objectSerializer->deserialize($object, $data, $context);
        $context->left();

        return $object;
    }

    public function supportsDenormalization(string $type, $data, DeserializationContext $context): bool
    {
        return class_exists($type) && $type !== \DateTime::class;
    }
}
