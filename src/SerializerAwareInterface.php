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
 * Interface SerializerAwareInterface.
 */
interface SerializerAwareInterface
{
    /**
     * @param SerializerInterface $serializer
     *
     * @return mixed
     */
    public function setSerializer(SerializerInterface $serializer): void;
}
