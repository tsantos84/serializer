<?php

namespace TSantos\Serializer\Normalizer;

use TSantos\Serializer\SerializationContext;
use TSantos\Serializer\SerializerAwareInterface;
use TSantos\Serializer\Traits\SerializerAwareTrait;

/**
 * Class JsonNormalizer
 * @package TSantos\Serializer\Normalizer
 */
class JsonNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    public function normalize($data, SerializationContext $context)
    {
        return $this->serializer->normalize($data->jsonSerialize(), $context);
    }

    public function supportsNormalization($data, SerializationContext $context): bool
    {
        return $data instanceof \JsonSerializable;
    }
}
