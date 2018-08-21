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

namespace TSantos\Serializer\DependencyInjection\Pimple;

use Doctrine\Instantiator\Instantiator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use TSantos\Serializer\Encoder\JsonEncoder;
use TSantos\Serializer\EncoderRegistry;
use TSantos\Serializer\EncoderRegistryInterface;
use TSantos\Serializer\EventDispatcher\EventDispatcher;
use TSantos\Serializer\EventDispatcher\EventDispatcherInterface;
use TSantos\Serializer\EventEmitterSerializer;
use TSantos\Serializer\HydratorLoaderInterface;
use TSantos\Serializer\Normalizer\ObjectNormalizer;
use TSantos\Serializer\NormalizerRegistry;
use TSantos\Serializer\NormalizerRegistryInterface;
use TSantos\Serializer\ObjectInstantiator\DoctrineInstantiator;
use TSantos\Serializer\ObjectInstantiator\ObjectInstantiatorInterface;
use TSantos\Serializer\Serializer;
use TSantos\Serializer\SerializerInterface;

/**
 * Class SerializerServiceProvider.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class SerializerServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['debug'] = false;
        $container['has_listener'] = false;
        $container['format'] = 'json';

        $container['circular_reference_handler'] = function () {
            return null;
        };

        $container[Serializer::class] = function ($container) {
            return new Serializer(
                $container[EncoderRegistryInterface::class]->get($container['format']),
                $container[NormalizerRegistryInterface::class]
            );
        };

        $container[EventEmitterSerializer::class] = function ($container) {
            return new EventEmitterSerializer(
                $container[EncoderRegistryInterface::class]->get($container['format']),
                $container[NormalizerRegistryInterface::class],
                $container[EventDispatcherInterface::class]
            );
        };

        $container[EventDispatcherInterface::class] = function () {
            return new EventDispatcher();
        };

        $container[SerializerInterface::class] = function ($container) {
            if ($container['has_listener']) {
                return $container[EventEmitterSerializer::class];
            }

            return $container[Serializer::class];
        };

        $container[EncoderRegistryInterface::class] = function () {
            return new EncoderRegistry();
        };

        $container->extend(EncoderRegistryInterface::class, function (EncoderRegistryInterface $registry) {
            return $registry->add(new JsonEncoder());
        });

        $container['normalizers'] = function ($container) {
            $objectNormalizer = new ObjectNormalizer(
                $container[HydratorLoaderInterface::class],
                $container['circular_reference_handler']
            );

            return [$objectNormalizer];
        };

        $container[NormalizerRegistryInterface::class] = function ($container) {
            return new NormalizerRegistry($container['normalizers']);
        };

        $container[ObjectInstantiatorInterface::class] = function ($container) {
            if (isset($container['custom_object_instantiator'])) {
                return $container['custom_object_instantiator'];
            }

            return new DoctrineInstantiator(new Instantiator());
        };
    }
}
