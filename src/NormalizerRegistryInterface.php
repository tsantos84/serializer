<?php

namespace TSantos\Serializer;

use TSantos\Serializer\Normalizer\NormalizerInterface;

/**
 * Interface NormalizerRegistryInterface
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
interface NormalizerRegistryInterface
{
    /**
     * @param NormalizerInterface $type
     * @return $this
     */
    public function add(NormalizerInterface $type);

    /**
     * @param mixed $data
     * @param SerializationContext $context
     * @return NormalizerInterface
     */
    public function get($data, SerializationContext $context): ?NormalizerInterface;
}
