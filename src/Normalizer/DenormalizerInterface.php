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

use TSantos\Serializer\DeserializationContext;

/**
 * Class DenormalizerInterface
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
interface DenormalizerInterface
{
    /**
     * Denormalizes the data instead of pass it to serializer instances.
     *
     * @param $data
     * @param DeserializationContext $context
     * @return mixed
     */
    public function denormalize($data, string $type, DeserializationContext $context);

    /**
     * Checks whether this denormalizer supports denormalization of the given type.
     *
     * @param string $type
     * @param $data
     * @param DeserializationContext $context
     * @return bool
     */
    public function supportsDenormalization(string $type, $data, DeserializationContext $context): bool;
}
