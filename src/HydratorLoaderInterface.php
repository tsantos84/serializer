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

/**
 * Class HydratorLoader.
 *
 * Load a hydrator for a class. It should not create new hydrator instances
 * for one class.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
interface HydratorLoaderInterface
{
    /**
     * @param string $class
     *
     * @return HydratorInterface
     */
    public function load(string $class): HydratorInterface;
}
