<?php

namespace TSantos\Serializer\Normalizer;

use TSantos\Serializer\SerializationContext;
use TSantos\Serializer\SerializerAwareInterface;
use TSantos\Serializer\Traits\SerializerAwareTrait;

/**
 * Class CollectionNormalizer
 * @package TSantos\Serializer\Normalizer
 */
class CollectionNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    public function normalize($data, SerializationContext $context)
    {
        $context->enter();
        $array = [];
        foreach ($data as $key => $value) {
            if (is_scalar($value)) {
                $array[$key] = $value;
                continue;
            }
            $array[$key] = $this->serializer->normalize($value, $context);
        }
        $context->left();

        return $array;
    }

    public function supportsNormalization($data, SerializationContext $context): bool
    {
        return is_iterable($data);
    }
}
