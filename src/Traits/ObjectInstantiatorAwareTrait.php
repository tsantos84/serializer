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

namespace TSantos\Serializer\Traits;

use TSantos\Serializer\ObjectInstantiator\ObjectInstantiatorInterface;

/**
 * Trait ObjectInstantiatorAwareTrait.
 */
trait ObjectInstantiatorAwareTrait
{
    /**
     * @var ObjectInstantiatorInterface
     */
    protected $instantiator;

    /**
     * @param ObjectInstantiatorInterface $instantiator
     */
    public function setInstantiator(ObjectInstantiatorInterface $instantiator): void
    {
        $this->instantiator = $instantiator;
    }
}
