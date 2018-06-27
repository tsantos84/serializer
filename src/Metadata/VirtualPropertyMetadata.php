<?php

declare(strict_types=1);
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Serializer\Metadata;

use Metadata\MethodMetadata;

/**
 * Class VirtualPropertyMetadata.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class VirtualPropertyMetadata extends MethodMetadata
{
    public $type;
    public $exposeAs;
    public $groups = ['Default'];
    public $readValueFilter;
    public $options = [];

    public function __construct($class, $name)
    {
        parent::__construct($class, $name);
        $this->exposeAs = $name;
    }

    public function serialize()
    {
        return \serialize([
            $this->name,
            $this->class,
            $this->type,
            $this->readValueFilter,
            $this->exposeAs,
            $this->groups,
            $this->options,
        ]);
    }

    public function unserialize($str)
    {
        $unserialized = \unserialize($str);

        list(
            $this->name,
            $this->class,
            $this->type,
            $this->readValueFilter,
            $this->exposeAs,
            $this->groups,
            $this->options
            ) = $unserialized;
    }
}
