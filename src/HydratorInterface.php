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

/**
 * Interface DataTransformerInterface
 *
 * @package Serializer
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
interface HydratorInterface
{
    /**
     * Extract the data from the given object and returns its array
     * representation.
     *
     * @param $object
     * @param SerializationContext $context
     * @return array
     */
    public function extract($object, SerializationContext $context): array;

    /**
     * Hydrate an object from the given array.
     *
     * @param $object
     * @param array $data
     * @param DeserializationContext $context
     * @return mixed
     */
    public function hydrate($object, array $data, DeserializationContext $context);
}
