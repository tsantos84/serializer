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
 * Interface ObjectSerializerInterface
 *
 * @package Serializer
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
interface SerializerClassInterface
{
    /**
     * @param $object
     * @param SerializationContext $context
     * @return array
     */
    public function serialize($object, SerializationContext $context): array;
}
