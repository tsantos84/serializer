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
use TSantos\Serializer\CodeDecorator\AbstractHydratorDecorator;
use TSantos\Serializer\CodeDecorator\ConstructorMethodDecorator;
use TSantos\Serializer\CodeDecorator\ExposedKeysDecorator;
use TSantos\Serializer\CodeDecorator\ExtractionDecorator;
use TSantos\Serializer\CodeDecorator\HydrationDecorator;
use TSantos\Serializer\CodeDecorator\NewInstanceMethodDecorator;
use TSantos\Serializer\CodeDecorator\PropertiesDecorator;
use TSantos\Serializer\CodeDecorator\ReflectionPropertyMethodDecorator;
use TSantos\Serializer\Compiler;
use TSantos\Serializer\Configuration;
use TSantos\Serializer\HydratorCodeGenerator;
use TSantos\Serializer\HydratorCodeWriter;
use TSantos\Serializer\HydratorLoader;
use TSantos\Serializer\ObjectInstantiator\ObjectInstantiatorInterface;

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
        $container['hydrator_namespace'] = 'App\\Hydrator';
        $container['generation_strategy'] = HydratorLoader::AUTOGENERATE_ALWAYS;

        $container[HydratorCodeGenerator::class] = function ($container) {
            return new HydratorCodeGenerator(
                $container[Configuration::class],
                [
                    new ExposedKeysDecorator(),
                    new ConstructorMethodDecorator(),
                    new AbstractHydratorDecorator(),
                    new ExtractionDecorator(),
                    new HydrationDecorator(),
                    new NewInstanceMethodDecorator(),
                    new PropertiesDecorator(),
                    new ReflectionPropertyMethodDecorator(),
                ]
            );
        };

        $container[Configuration::class] = function ($container) {
            return new Configuration(
                $container['hydrator_namespace'],
                $container['hydrator_dir'],
                $container['generation_strategy']
            );
        };

        $container[HydratorCodeWriter::class] = function ($container) {
            $container['directory_creator']($container['hydrator_dir']);

            if (!\is_writable($container['hydrator_dir'])) {
                throw new \InvalidArgumentException(\sprintf('The hydrator directory "%s" is not writable.', $container['hydrator_dir']));
            }

            return new HydratorCodeWriter($container[Configuration::class]);
        };

        $container[HydratorLoader::class] = function ($container) {
            return new HydratorLoader(
                $container[Configuration::class],
                $container[MetadataFactoryInterface::class],
                $container[Compiler::class],
                $container[ObjectInstantiatorInterface::class]
            );
        };

        $container[Compiler::class] = function ($container) {
            return new Compiler(
                $container[Configuration::class],
                $container[HydratorCodeGenerator::class],
                $container[HydratorCodeWriter::class]
            );
        };
    }
}
