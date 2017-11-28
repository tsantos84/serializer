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
    public $accessor;
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
            $this->accessor,
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
            $this->accessor,
            $this->exposeAs,
            $this->groups,
            $this->modifier
        ) = $unserialized;

        if (false !== strpos($this->accessor, '(')) {
            $this->getterRef = new \ReflectionMethod($this->class, $this->accessor);
        }
    }
}
