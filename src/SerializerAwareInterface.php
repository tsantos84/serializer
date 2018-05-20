<?php

namespace TSantos\Serializer;

/**
 * Interface SerializerAwareInterface
 * @package TSantos\Serializer
 */
interface SerializerAwareInterface
{
    /**
     * @param SerializerInterface $serializer
     * @return mixed
     */
    public function setSerializer(SerializerInterface $serializer): void;
}
