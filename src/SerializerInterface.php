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
 * Class Serializer
 *
 * @package Serializer
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
interface SerializerInterface
{
    /**
     * Converts any value to the given format.
     *
     * @param mixed $data
     * @param string $format
     * @param SerializationContext $context
     * @return string
     */
    public function serialize($data, string $format, SerializationContext $context = null): string;

    /**
     * Converts any value in array.
     *
     * @param mixed $data
     * @param SerializationContext|null $context
     * @return array
     */
    public function normalize($data, SerializationContext $context = null);
}
