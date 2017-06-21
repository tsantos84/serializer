<?php

namespace Serializer;

use Metadata\ClassMetadata;

/**
 * Interface ObjectSerializertInterface
 *
 * @package Serializer
 * @author Tales Santos <tales.maxmilhas@gmail.com>
 */
interface SerializerClassInterface
{
    /**
     * @param ClassMetadata $metadata
     * @param $object
     * @param Serializer $serializer
     * @return array
     */
    public function serialize(ClassMetadata $metadata, $object, Serializer $serializer): array;
}
