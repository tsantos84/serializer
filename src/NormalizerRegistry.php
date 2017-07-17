<?php

namespace TSantos\Serializer;

use TSantos\Serializer\Normalizer\NormalizerInterface;

/**
 * Class NormalizerRegistry
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class NormalizerRegistry implements NormalizerRegistryInterface
{
    /**
     * @var NormalizerInterface[]
     */
    private $normalizers = [];

    /**
     * @param NormalizerInterface $type
     * @return $this
     */
    public function add(NormalizerInterface $type)
    {
        $this->normalizers[] = $type;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function get($data, SerializationContext $context): ?NormalizerInterface
    {
        foreach ($this->normalizers as $normalizer) {
            if ($normalizer->supportsNormalization($data, $context)) {
                return $normalizer;
            }
        }

        return null;
    }
}
