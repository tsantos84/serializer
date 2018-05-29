<?php

namespace TSantos\Serializer\Normalizer;

use TSantos\Serializer\SerializationContext;

/**
 * Class ScalarNormalizer
 * @package TSantos\Serializer\Normalizer
 */
class ScalarNormalizer implements NormalizerInterface
{
    public function normalize($data, SerializationContext $context)
    {
        return $data;
    }

    public function supportsNormalization($data, SerializationContext $context): bool
    {
        return is_scalar($data) || is_null($data);
    }
}
