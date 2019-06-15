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

use Pimple\Container;
use Psr\Container\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use TSantos\Serializer\DependencyInjection\Pimple\HydratorServiceProvider;
use TSantos\Serializer\DependencyInjection\Pimple\MetadataServiceProvider;
use TSantos\Serializer\DependencyInjection\Pimple\SerializerServiceProvider;

return function (Container $container) {
    $container[ContainerInterface::class] = function ($container) {
        return new \Pimple\Psr11\Container($container);
    };

    $container[Filesystem::class] = function (): Filesystem {
        return new Filesystem();
    };

    $container->register(new HydratorServiceProvider());
    $container->register(new MetadataServiceProvider());
    $container->register(new SerializerServiceProvider());
};
