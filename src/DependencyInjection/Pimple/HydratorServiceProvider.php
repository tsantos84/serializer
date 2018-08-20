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

use Metadata\MetadataFactoryInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use TSantos\Serializer\CodeDecorator\ExposedKeysDecorator;
use TSantos\Serializer\CodeDecorator\ExtractionDecorator;
use TSantos\Serializer\CodeDecorator\HydrationDecorator;
use TSantos\Serializer\CodeDecorator\NewInstanceMethodDecorator;
use TSantos\Serializer\CodeDecorator\PropertiesDecorator;
use TSantos\Serializer\CodeDecorator\ReflectionPropertyMethodDecorator;
use TSantos\Serializer\HydratorCodeGenerator;
use TSantos\Serializer\HydratorCodeWriter;
use TSantos\Serializer\HydratorLoader;

/**
 * Class HydratorServiceProvider.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class HydratorServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['hydrator_dir'] = \sys_get_temp_dir().'/serializer/hydrators';
        $container['generation_strategy'] = HydratorLoader::AUTOGENERATE_ALWAYS;

        $container[HydratorCodeGenerator::class] = function () {
            return new HydratorCodeGenerator([
                new ExposedKeysDecorator(),
                new ExtractionDecorator(),
                new HydrationDecorator(),
                new NewInstanceMethodDecorator(),
                new PropertiesDecorator(),
                new ReflectionPropertyMethodDecorator(),
            ]);
        };

        $container[HydratorCodeWriter::class] = function ($container) {
            $container['directory_creator']($container['hydrator_dir']);

            if (!\is_writable($container['hydrator_dir'])) {
                throw new \InvalidArgumentException(\sprintf('The hydrator directory "%s" is not writable.', $container['hydrator_dir']));
            }

            return new HydratorCodeWriter($container['hydrator_dir']);
        };

        $container[HydratorLoader::class] = function ($container) {
            return new HydratorLoader(
                $container[MetadataFactoryInterface::class],
                $container[HydratorCodeGenerator::class],
                $container[HydratorCodeWriter::class],
                $container['generation_strategy']
            );
        };
    }
}
