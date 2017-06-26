<?php

namespace TSantos\Serializer;

use TSantos\Serializer\Type\TypeInterface;

/**
 * Class TypeRegistry
 *
 * @package Serializer
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class TypeRegistry implements TypeRegistryInterface
{
    /**
     * @var TypeInterface[]
     */
    private $types;

    /**
     * @param TypeInterface $type
     * @return TypeRegistryInterface
     */
    public function addType(TypeInterface $type)
    {
        $this->types[$type->getName()] = $type;
        return $this;
    }

    /**
     * @param string $name
     * @return TypeInterface
     */
    public function get(string $name): TypeInterface
    {
        if (!isset($this->types[$name])) {
            throw new \InvalidArgumentException('There is no type registered with name ' . $name);
        }

        return $this->types[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->types[$name]);
    }
}
