<?php

namespace Serializer\Type;

use Serializer\Metadata\PropertyMetadata;

/**
 * Class ScalarType
 *
 * @package Serializer\Type
 * @author Tales Santos <tales.maxmilhas@gmail.com>
 */
class FloatType implements TypeInterface
{
    /**
     * @param string $getter
     * @param PropertyMetadata $metadata
     * @return string
     */
    public function modify(string $getter, PropertyMetadata $metadata): string
    {
        return sprintf('(float) %s', $getter);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'float';
    }
}
