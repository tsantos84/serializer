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

use TSantos\Serializer\Encoder\EncoderInterface;

/**
 * Class EncoderRegistry
 *
 * @package Serializer
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class EncoderRegistry implements EncoderRegistryInterface
{
    /**
     * @var EncoderInterface[]
     */
    private $encoders = [];

    /**
     * @param EncoderInterface $type
     * @return EncoderRegistryInterface
     */
    public function add(EncoderInterface $type)
    {
        $this->encoders[$type->getFormat()] = $type;
        return $this;
    }

    /**
     * @param string $name
     * @return EncoderInterface
     */
    public function get(string $name): EncoderInterface
    {
        if (!isset($this->encoders[$name])) {
            throw new \InvalidArgumentException('There is no type registered with name ' . $name);
        }

        return $this->encoders[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->encoders[$name]);
    }
}
