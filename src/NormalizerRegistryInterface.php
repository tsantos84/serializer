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
