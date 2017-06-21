<?php

namespace Serializer\Type;

use Serializer\Metadata\PropertyMetadata;

/**
 * Class BooleanType
 *
 * @package Serializer\Type
 * @author Tales Santos <tales.maxmilhas@gmail.com>
 */
class BooleanType implements TypeInterface
{
    /**
     * @param string $getter
     * @param PropertyMetadata $metadata
     * @return string
     */
    public function modify(string $getter, PropertyMetadata $metadata): string
    {
        return sprintf('(bool) %s', $getter);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'boolean';
    }
}
