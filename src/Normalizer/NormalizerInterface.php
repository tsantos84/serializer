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

/**
 * Class NormalizerInterface
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
interface NormalizerInterface
{
    /**
     * Normalizes the data instead of pass it to serializer instances.
     *
     * @param $data
     * @param SerializationContext $context
     * @return mixed
     */
    public function normalize($data, SerializationContext $context);

    /**
     * Checks whether this normalizer supports normalization of the given data.
     *
     * @param $data
     * @param SerializationContext $context
     * @return bool
     */
    public function supportsNormalization($data, SerializationContext $context): bool;
}
