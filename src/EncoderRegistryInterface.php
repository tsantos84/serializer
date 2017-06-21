<?php

namespace Serializer;

use Serializer\Encoder\EncoderInterface;

/**
 * Class TypeRegistry
 *
 * @package Serializer
 * @author Tales Santos <tales.maxmilhas@gmail.com>
 */
interface EncoderRegistryInterface
{
    /**
     * @param EncoderInterface $type
     * @return $this
     */
    public function add(EncoderInterface $type);

    /**
     * @param string $name
     * @return EncoderInterface
     */
    public function get(string $name): EncoderInterface;

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;
}
