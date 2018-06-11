<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

    /**
     * @param \JsonSerializable $data
     * @param SerializationContext $context
     * @return mixed
     */
    public function normalize($data, SerializationContext $context)
    {
        return $this->serializer->normalize($data->jsonSerialize(), $context);
    }

    public function supportsNormalization($data, SerializationContext $context): bool
    {
        return $data instanceof \JsonSerializable;
    }
}
