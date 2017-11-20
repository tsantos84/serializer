<?php

namespace TSantos\Serializer\Metadata;

use Metadata\MethodMetadata;

/**
 * Class VirtualPropertyMetadata
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class VirtualPropertyMetadata extends MethodMetadata
{
    public $type;
    public $exposeAs;
    public $groups;
    public $modifier;

    public function serialize()
    {
        return serialize([
            $this->name,
            $this->class,
            $this->type,
            $this->modifier,
            $this->exposeAs,
            $this->groups
        ]);
    }

    public function unserialize($str)
    {
        $unserialized = unserialize($str);

        list(
            $this->name,
            $this->class,
            $this->type,
            $this->modifier,
            $this->exposeAs,
            $this->groups
        ) = $unserialized;
    }
}
