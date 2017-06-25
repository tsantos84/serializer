<?php

namespace TSantos\Serializer;

/**
 * Interface ObjectSerializertInterface
 *
 * @package Serializer
 * @author Tales Santos <tales.maxmilhas@gmail.com>
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
