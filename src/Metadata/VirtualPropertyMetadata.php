<?php
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
 * Class VirtualPropertyMetadata
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class VirtualPropertyMetadata extends MethodMetadata
{
    public $type = 'string';
    public $exposeAs;
    public $groups = ['Default'];
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
