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
 * Class Serializer.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
interface SerializerInterface
{
    /**
     * Converts any value to the given format.
     *
     * @param mixed                $data
     * @param SerializationContext $context
     *
     * @return string
     */
    public function serialize($data, SerializationContext $context = null): string;

    /**
     * Normalize a data by converting it from some type to array.
     *
     * This operation is like a "toArray" conversion.
     *
     * @param mixed                     $data
     * @param SerializationContext|null $context
     *
     * @return mixed
     */
    public function normalize($data, SerializationContext $context = null);

    /**
     * Deserialize the given in object of type $type.
     *
     * @param string                      $content
     * @param string                      $type
     * @param DeserializationContext|null $context
     *
     * @return mixed
     */
    public function deserialize(string $content, string $type, DeserializationContext $context = null);

    /**
     * Denormalize a data by converting it from array to some type.
     *
     * This operation is like a "fromArray" conversion.
     *
     * @param array                       $data
     * @param string                      $type
     * @param DeserializationContext|null $context
     *
     * @return mixed
     */
    public function denormalize($data, string $type, DeserializationContext $context = null);
}
