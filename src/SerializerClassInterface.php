<?php

namespace Serializer;

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
     * @return array
     */
    public function serialize($object): array;
}
