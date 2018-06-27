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
 * Class NormalizerRegistry.
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
     * @var array
     */
    private $cachedNormalizers = [
        'normalizer' => [],
        'denormalizer' => [],
    ];

    /**
     * @param $normalizer
     *
     * @return $this
     */
    public function add($normalizer)
    {
        $this->normalizers[] = $normalizer;

        return $this;
    }

    public function unshift($normalizer)
    {
        array_unshift($this->normalizers, $normalizer);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNormalizer($data, SerializationContext $context): ?NormalizerInterface
    {
        $type = is_object($data) ? get_class($data) : gettype($data);

        if (isset($this->cachedNormalizers['normalizer'][$type])) {
            return $this->cachedNormalizers['normalizer'][$type];
        }

        foreach ($this->normalizers as $normalizer) {
            if ($normalizer instanceof NormalizerInterface && $normalizer->supportsNormalization($data, $context)) {
                if ($normalizer instanceof CacheableNormalizerInterface && $normalizer->canBeCachedByType()) {
                    $this->cachedNormalizers['normalizer'][$type] = $normalizer;
                }

                return $normalizer;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDenormalizer($data, string $type, DeserializationContext $context): ?DenormalizerInterface
    {
        if (isset($this->cachedNormalizers['denormalizer'][$type])) {
            return $this->cachedNormalizers['denormalizer'][$type];
        }

        foreach ($this->normalizers as $denormalizer) {
            if ($denormalizer instanceof DenormalizerInterface
                && $denormalizer->supportsDenormalization($type, $data, $context)) {
                if ($denormalizer instanceof CacheableNormalizerInterface && $denormalizer->canBeCachedByType()) {
                    $this->cachedNormalizers['denormalizer'][$type] = $denormalizer;
                }

                return $denormalizer;
            }
        }

        return null;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->normalizers);
    }
}
