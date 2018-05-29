<?php

namespace TSantos\Serializer\Traits;

use TSantos\Serializer\SerializerInterface;

/**
 * Trait SerializerAwareTrait
 * @package TSantos\Serializer\Traits
 */
trait SerializerAwareTrait
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }
}
