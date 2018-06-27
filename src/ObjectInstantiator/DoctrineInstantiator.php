<?php

declare(strict_types=1);
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Serializer\ObjectInstantiator;

use Doctrine\Instantiator\InstantiatorInterface;
use TSantos\Serializer\DeserializationContext;

/**
 * Class DoctrineInstantiatorFactory.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class DoctrineInstantiator implements ObjectInstantiatorInterface
{
    /**
     * @var InstantiatorInterface
     */
    private $instantiator;

    /**
     * DoctrineInstantiatorFactory constructor.
     *
     * @param InstantiatorInterface $instantiator
     */
    public function __construct(InstantiatorInterface $instantiator)
    {
        $this->instantiator = $instantiator;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $type, array $data, DeserializationContext $context)
    {
        return $this->instantiator->instantiate($type);
    }
}
