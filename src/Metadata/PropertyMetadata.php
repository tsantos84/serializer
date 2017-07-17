<?php

namespace TSantos\Serializer\Metadata;

use Metadata\PropertyMetadata as BasePropertyMetadata;

/**
 * Class PropertyMetadata
 *
 * @package Serializer\Metadata
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class PropertyMetadata extends BasePropertyMetadata
{
    public $type;
    public $getter;
    public $modifier;
    /** @var  \ReflectionMethod */
    public $getterRef;
    public $exposeAs;
    public $groups;
}
