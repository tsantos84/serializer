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
    /** @var  \ReflectionMethod */
    public $getterRef;
    public $exposeAs;
    public $groups;
    public $modifier;

    public function serialize()
    {
        return serialize([
            $this->name,
            $this->class,
            $this->type,
            $this->getter,
            $this->exposeAs,
            $this->groups,
            $this->modifier
        ]);
    }

    public function unserialize($str)
    {
        $unserialized = unserialize($str);

        list(
            $this->name,
            $this->class,
            $this->type,
            $this->getter,
            $this->exposeAs,
            $this->groups,
            $this->modifier
        ) = $unserialized;

        $this->getterRef = new \ReflectionMethod($this->class, $this->getter);
    }
}
