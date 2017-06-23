<?php

namespace Serializer\Metadata;

use Metadata\MethodMetadata;

/**
 * Class VirtualPropertyMetadata
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class VirtualPropertyMetadata extends MethodMetadata
{
    public $exposeAs;
    public $groups;
}
