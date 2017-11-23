<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
