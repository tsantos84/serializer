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

namespace TSantos\Serializer\ObjectInstantiator;

use TSantos\Serializer\DeserializationContext;

/**
 * Interface ObjectInstantiatorInterface.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
interface ObjectInstantiatorInterface
{
    /**
     * Creates a new instance of $type.
     *
     * @param string                 $type
     * @param array                  $data
     * @param DeserializationContext $context
     *
     * @return object
     */
    public function create(string $type, array $data, DeserializationContext $context);
}
