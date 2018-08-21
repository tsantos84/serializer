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
        /** @var HydratorInterface $hydrator */
        $hydrator = $this->instantiate($classMetadata);

        if ($hydrator instanceof SerializerAwareInterface) {
            $hydrator->setSerializer($this->container->get(SerializerInterface::class));
        }

        if ($hydrator instanceof HydratorLoaderAwareInterface) {
            $hydrator->setLoader($this->container->get(HydratorLoaderInterface::class));
        }

        if ($hydrator instanceof ObjectInstantiatorAwareInterface) {
            $hydrator->setInstantiator($this->container->get(ObjectInstantiatorInterface::class));
        }

        return $hydrator;
    }

    private function instantiate(ClassMetadata $classMetadata): HydratorInterface
    {
        $reflection = new \ReflectionClass($this->configuration->getFqnClassName($classMetadata));

        if (empty($classMetadata->hydratorConstructArgs)) {
            return $reflection->newInstanceWithoutConstructor();
        }

        $args = $this->resolveArgs($reflection, $classMetadata);

        return $reflection->newInstanceArgs($args);
    }

    private function resolveArgs(\ReflectionClass $hydratorClass, ClassMetadata $classMetadata): array
    {
        $constructor = $hydratorClass->getConstructor();
        $configuredArgs = $classMetadata->hydratorConstructArgs;

        $args = [];

        foreach ($constructor->getParameters() as $param) {
            $arg = $configuredArgs[$param->getName()];

            $argValue = $arg['value'];

            if (\is_string($argValue) && 0 === \mb_strpos($argValue, '@')) {
                $argValue = $this->container->get(\mb_substr($argValue, 1));
            }

            $args[] = $argValue;
        }

        return $args;
    }
}
