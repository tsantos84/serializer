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
use TSantos\Serializer\DependencyInjection\Pimple\HydratorServiceProvider;
use TSantos\Serializer\DependencyInjection\Pimple\MetadataServiceProvider;
use TSantos\Serializer\DependencyInjection\Pimple\SerializerServiceProvider;

$container = new Container();

$container['directory_creator'] = $container->protect(function (string $dir) {
    if (\is_dir($dir)) {
        return;
    }

    if (false === @\mkdir($dir, 0777, true) && false === \is_dir($dir)) {
        throw new \RuntimeException(\sprintf('Could not create directory "%s".', $dir));
    }
});

$container->register(new HydratorServiceProvider());
$container->register(new MetadataServiceProvider());
$container->register(new SerializerServiceProvider());

return $container;
