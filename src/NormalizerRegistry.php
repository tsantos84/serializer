<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Serializer;

use TSantos\Serializer\Normalizer\DenormalizerInterface;
use TSantos\Serializer\Normalizer\NormalizerInterface;

/**
 * Class NormalizerRegistry
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class NormalizerRegistry implements NormalizerRegistryInterface
{
    /**
     * @var array
     */
    private $normalizers = [];

    /**
     * @param $normalizer
     * @return $this
     */
    public function add($normalizer)
    {
        $this->normalizers[] = $normalizer;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getNormalizer($data, SerializationContext $context): ?NormalizerInterface
    {
        foreach ($this->normalizers as $normalizer) {
            if ($normalizer instanceof NormalizerInterface && $normalizer->supportsNormalization($data, $context)) {
                return $normalizer;
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getDenormalizer($data, string $type, DeserializationContext $context): ?DenormalizerInterface
    {
        foreach ($this->normalizers as $denormalizer) {
            if ($denormalizer instanceof DenormalizerInterface && $denormalizer->supportsDenormalization($type, $data, $context)) {
                return $denormalizer;
            }
        }

        return null;
    }
}
