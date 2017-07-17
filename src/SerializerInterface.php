<?php

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
    public function toArray($data, SerializationContext $context = null);
}
