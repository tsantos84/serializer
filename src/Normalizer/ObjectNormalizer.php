<?php

namespace TSantos\Serializer\Normalizer;

use TSantos\Serializer\SerializationContext;
use TSantos\Serializer\SerializerAwareInterface;
use TSantos\Serializer\SerializerClassLoader;
use TSantos\Serializer\Traits\SerializerAwareTrait;

/**
 * Class ObjectNormalizer
 * @package TSantos\Serializer\Normalizer
 */
class ObjectNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    /**
     * @var SerializerClassLoader
     */
    private $classLoader;

    /**
     * ObjectNormalizer constructor.
     * @param SerializerClassLoader $classLoader
     */
    public function __construct(SerializerClassLoader $classLoader)
    {
        $this->classLoader = $classLoader;
    }

    public function normalize($data, SerializationContext $context)
    {
        $objectSerializer = $this->classLoader->load(get_class($data), $this->serializer);

        $context->enter($data);
        $array = $objectSerializer->serialize($data, $context);
        $context->left();

        return $array;
    }

    public function supportsNormalization($data, SerializationContext $context): bool
    {
        return is_object($data) && !is_iterable($data);
    }
}
