<?php

namespace Serializer\Metadata;

use Metadata\PropertyMetadata as BasePropertyMetadata;

/**
 * Class PropertyMetadata
 *
 * @package Serializer\Metadata
 * @author Tales Santos <tales.maxmilhas@gmail.com>
 */
class PropertyMetadata extends BasePropertyMetadata
{
    public $type;
    public $getter;
    public $exposeAs;
}
