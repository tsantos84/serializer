<?php

namespace TSantos\Serializer\Type;

use TSantos\Serializer\Metadata\PropertyMetadata;

/**
 * Interface TypeInterface
 *
 * @package Serializer\Type
 * @author Tales Santos <tales.maxmilhas@gmail.com>
 */
interface TypeInterface
{
    /**
     * @param string $getter
     * @param PropertyMetadata $metadata
     * @return string
     */
    public function modify(string $getter, PropertyMetadata $metadata): string;

    /**
     * @return string
     */
    public function getName(): string;
}
