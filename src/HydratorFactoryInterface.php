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

use TSantos\Serializer\Metadata\ClassMetadata;

/**
 * Interface HydratorFactoryInterface.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
interface HydratorFactoryInterface
{
    /**
     * Create a new instance of the hydrator.
     *
     * @param ClassMetadata $classMetadata
     *
     * @return HydratorInterface
     */
    public function newInstance(ClassMetadata $classMetadata): HydratorInterface;
}
