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
use Psr\Container\ContainerInterface;
use TSantos\Serializer\CodeDecorator\AbstractHydratorDecorator;
use TSantos\Serializer\CodeDecorator\ClassMetadataDecorator;
use TSantos\Serializer\CodeDecorator\ConstructorMethodDecorator;
use TSantos\Serializer\CodeDecorator\ExposedKeysDecorator;
use TSantos\Serializer\CodeDecorator\ExtractionDecorator;
use TSantos\Serializer\CodeDecorator\HydrationDecorator;
use TSantos\Serializer\CodeDecorator\NewInstanceMethodDecorator;
use TSantos\Serializer\CodeDecorator\PropertiesDecorator;
use TSantos\Serializer\Configuration;
use TSantos\Serializer\HydratorCodeGenerator;
use TSantos\Serializer\HydratorCodeWriter;
use TSantos\Serializer\HydratorCompiler;
use TSantos\Serializer\HydratorCompilerInterface;
use TSantos\Serializer\HydratorFactory;
use TSantos\Serializer\HydratorFactoryInterface;
use TSantos\Serializer\HydratorLoader;
use TSantos\Serializer\HydratorLoaderInterface;

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
        $container['generation_strategy'] = HydratorCompiler::AUTOGENERATE_ALWAYS;

        $container[Configuration::class] = function ($container) {
            return new Configuration(
                $container['hydrator_namespace'],
                $container['hydrator_dir'],
                $container['generation_strategy']
            );
        };

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
                    new ClassMetadataDecorator(),
                ]
            );
        };

        $container[HydratorCodeWriter::class] = function ($container) {
            $container['directory_creator']($container['hydrator_dir']);

            if (!\is_writable($container['hydrator_dir'])) {
                throw new \InvalidArgumentException(\sprintf('The hydrator directory "%s" is not writable.', $container['hydrator_dir']));
            }

            return new HydratorCodeWriter($container[Configuration::class]);
        };

        $container[HydratorLoaderInterface::class] = function ($container) {
            return new HydratorLoader(
                $container[Configuration::class],
                $container[MetadataFactoryInterface::class],
                $container[HydratorCompilerInterface::class],
                $container[HydratorFactoryInterface::class]
            );
        };

        $container[HydratorCompilerInterface::class] = function ($container) {
            return new HydratorCompiler(
                $container[Configuration::class],
                $container[HydratorCodeGenerator::class],
                $container[HydratorCodeWriter::class]
            );
        };

        $container[HydratorFactoryInterface::class] = function ($container) {
            return new HydratorFactory(
                $container[Configuration::class],
                $container[ContainerInterface::class]
            );
        };
    }
}
