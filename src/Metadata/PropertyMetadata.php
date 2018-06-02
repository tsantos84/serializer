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
    /** @var  \ReflectionMethod */
    public $getterRef;
    public $getter;
    public $readValue;

    /** @var  \ReflectionMethod */
    public $setterRef;
    public $setter;
    public $writeValue;

    public $exposeAs;
    public $groups = ['Default'];
    public $modifier;
    public $readOnly = false;

    public function __construct($class, $name)
    {
        parent::__construct($class, $name);
        $this->exposeAs = $name;
    }

    public function setGetter(string $getter): void
    {
        $this->getter = $getter;
        $this->getterRef = new \ReflectionMethod($this->class, $getter);
    }

    public function setSetter(string $setter): void
    {
        $this->setter = $setter;
        $this->setterRef = new \ReflectionMethod($this->class, $setter);
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

        if ($this->getter) {
            $this->getterRef = new \ReflectionMethod($this->class, $this->getter);
        }

        if ($this->setter) {
            $this->setterRef = new \ReflectionMethod($this->class, $this->setter);
        }
    }
}
