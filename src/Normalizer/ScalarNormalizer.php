<?php

namespace TSantos\Serializer\Normalizer;

use TSantos\Serializer\CacheableNormalizerInterface;
use TSantos\Serializer\DeserializationContext;
use TSantos\Serializer\SerializationContext;

/**
 * Class ScalarNormalizer
 * @package TSantos\Serializer\Normalizer
 */
class ScalarNormalizer implements NormalizerInterface, DenormalizerInterface, CacheableNormalizerInterface
{
    public function normalize($data, SerializationContext $context)
    {
        return $data;
    }

    public function supportsNormalization($data, SerializationContext $context): bool
    {
        return is_scalar($data) || is_null($data);
    }

    public function denormalize($data, string $type, DeserializationContext $context)
    {
        return $data;
    }

    public function supportsDenormalization(string $type, $data, DeserializationContext $context): bool
    {
        return is_scalar($data) || is_null($data);
    }

    public function canBeCachedByType(): bool
    {
        return true;
    }
}
