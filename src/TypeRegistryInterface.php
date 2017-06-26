<?php

namespace TSantos\Serializer;

use TSantos\Serializer\Type\TypeInterface;

/**
 * Class TypeRegistry
 *
 * @package Serializer
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
interface TypeRegistryInterface
{
    /**
     * @param TypeInterface $type
     * @return $this
     */
    public function addType(TypeInterface $type);

    /**
     * @param string $name
     * @return TypeInterface
     */
    public function get(string $name): TypeInterface;

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;
}
