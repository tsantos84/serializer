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

use Psr\Container\ContainerInterface;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\ObjectInstantiator\ObjectInstantiatorInterface;

/**
 * Class HydratorFactory.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class HydratorFactory implements HydratorFactoryInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * HydratorFactory constructor.
     *
     * @param Configuration      $configuration
     * @param ContainerInterface $container
     */
    public function __construct(Configuration $configuration, ContainerInterface $container)
    {
        $this->configuration = $configuration;
        $this->container = $container;
    }

    public function newInstance(ClassMetadata $classMetadata): HydratorInterface
    {
        $fqn = $this->configuration->getFqnClassName($classMetadata);

        /** @var HydratorInterface $hydrator */
        $hydrator = new $fqn($this->container->get(ObjectInstantiatorInterface::class));

        if ($hydrator instanceof SerializerAwareInterface) {
            $hydrator->setSerializer($this->container->get(SerializerInterface::class));
        }

        if ($hydrator instanceof HydratorLoaderAwareInterface) {
            $hydrator->setLoader($this->container->get(HydratorLoader::class));
        }

        return $hydrator;
    }
}
