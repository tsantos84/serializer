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

namespace TSantos\Serializer;

use TSantos\Serializer\Encoder\EncoderInterface;

/**
 * Class TypeRegistry.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
interface EncoderRegistryInterface
{
    /**
     * @param EncoderInterface $type
     *
     * @return $this
     */
    public function add(EncoderInterface $type);

    /**
     * @param string $name
     *
     * @return EncoderInterface
     */
    public function get(string $name): EncoderInterface;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * @return bool
     */
    public function isEmpty(): bool;
}
