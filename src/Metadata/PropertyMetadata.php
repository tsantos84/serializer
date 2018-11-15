<?php

declare(strict_types=1);

/*
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
 * Class PropertyMetadata.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class PropertyMetadata extends BasePropertyMetadata
{
    use PropertyTrait;

    /** @var \ReflectionMethod */
    public $getterRef;
    public $getter;

    /** @var \ReflectionMethod */
    public $setterRef;
    public $setter;
    public $writeValueFilter;

    /** @var bool */
    public $isConstructArg = false;

    public $readOnly = false;

    public $reflection;

    public function __construct(string $class, string $name)
    {
        parent::__construct($class, $name);
        $this->reflection = new \ReflectionProperty($class, $name);
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
        return \serialize([
            $this->name,
            $this->class,
            $this->type,
            $this->getter,
            $this->setter,
            $this->exposeAs,
            $this->groups,
            $this->readValueFilter,
            $this->writeValueFilter,
            $this->readOnly,
            $this->options,
            $this->isConstructArg,
        ]);
    }

    public function unserialize($str)
    {
        $unserialized = \unserialize($str);

        list(
            $this->name,
            $this->class,
            $this->type,
            $this->getter,
            $this->setter,
            $this->exposeAs,
            $this->groups,
            $this->readValueFilter,
            $this->writeValueFilter,
            $this->readOnly,
            $this->options,
            $this->isConstructArg
            ) = $unserialized;

        if ($this->getter) {
            $this->getterRef = new \ReflectionMethod($this->class, $this->getter);
        }

        if ($this->setter) {
            $this->setterRef = new \ReflectionMethod($this->class, $this->setter);
        }

        $this->reflection = new \ReflectionProperty($this->class, $this->name);
        $this->reflection->setAccessible(true);
    }
}
