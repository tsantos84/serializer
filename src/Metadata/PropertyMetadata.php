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
    public $type = 'string';
    public $getter;
    /** @var  \ReflectionMethod */
    public $getterRef;
    public $setter;
    /** @var  \ReflectionMethod */
    public $setterRef;
    public $exposeAs;
    public $groups = ['Default'];
    public $modifier;
    public $readOnly = false;

    public function __construct($class, $name)
    {
        parent::__construct($class, $name);
        $this->exposeAs = $name;
    }

    public function serialize()
    {
        return serialize([
            $this->name,
            $this->class,
            $this->type,
            $this->getter,
            $this->setter,
            $this->exposeAs,
            $this->groups,
            $this->modifier,
            $this->readOnly
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
            $this->setter,
            $this->exposeAs,
            $this->groups,
            $this->modifier,
            $this->readOnly
        ) = $unserialized;

        $getter = substr($this->getter, 0, strpos($this->getter, '('));
        $this->getterRef = new \ReflectionMethod($this->class, $getter);
        $setter = substr($this->setter, 0, strpos($this->setter, '('));
        $this->setterRef = new \ReflectionMethod($this->class, $setter);
    }
}
