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

use Metadata\PropertyMetadata as BasePropertyMetadata;

/**
 * Class PropertyMetadata.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class PropertyMetadata extends BasePropertyMetadata
{
    public $type;
    /** @var \ReflectionMethod */
    public $getterRef;
    public $getter;
    public $readValueFilter;

    /** @var \ReflectionMethod */
    public $setterRef;
    public $setter;
    public $writeValueFilter;

    public $exposeAs;
    public $groups = ['Default'];
    public $readOnly = false;

    public $options = [];

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
            $this->options
            ) = $unserialized;

        if ($this->getter) {
            $this->getterRef = new \ReflectionMethod($this->class, $this->getter);
        }

        if ($this->setter) {
            $this->setterRef = new \ReflectionMethod($this->class, $this->setter);
        }
    }

    public function isScalarType(): bool
    {
        return \in_array($this->type, ['integer', 'string', 'float', 'boolean'], true);
    }

    public function isScalarCollectionType(): bool
    {
        if (false === $pos = \strpos($this->type, '[]')) {
            return false;
        }

        $type = \substr($this->type, 0, $pos);

        return \in_array($type, ['integer', 'string', 'float', 'boolean'], true);
    }

    public function getTypeOfCollection(): ?string
    {
        if (false === $pos = \strpos($this->type, '[]')) {
            return null;
        }

        return \substr($this->type, 0, $pos);
    }
}
