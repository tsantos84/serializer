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
use Nette\PhpGenerator\Printer;
use Nette\PhpGenerator\PsrPrinter;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use TSantos\Serializer\CodeDecorator\AbstractHydratorDecorator;
use TSantos\Serializer\CodeDecorator\ClassMetadataDecorator;
use TSantos\Serializer\CodeDecorator\ConstructorMethodDecorator;
use TSantos\Serializer\CodeDecorator\ExposedKeysDecorator;
use TSantos\Serializer\CodeDecorator\ExtractionDecorator;
use TSantos\Serializer\CodeDecorator\HydrationDecorator;
use TSantos\Serializer\CodeDecorator\NewInstanceMethodDecorator;
use TSantos\Serializer\CodeDecorator\Template;
use TSantos\Serializer\Configuration;
use TSantos\Serializer\Exception\FilesystemException;
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
        $container['generation_strategy'] = HydratorLoader::COMPILE_IF_NOT_EXISTS;
        $container['property_group_enabled'] = false;
        $container['max_depth_check_enabled'] = false;

        $container[Configuration::class] = function ($container) {
            return new Configuration(
                $container['hydrator_namespace'],
                $container['hydrator_dir'],
                $container['generation_strategy'],
                $container['max_depth_check_enabled']
            );
        };

        $container[Printer::class] = function () {
            return new PsrPrinter();
        };

        $container[HydratorCodeGenerator::class] = function ($container) {
            $generator = new HydratorCodeGenerator(
                $container[Configuration::class],
                $container[Printer::class],
                [
                    new ConstructorMethodDecorator(),
                    new AbstractHydratorDecorator(),
                    new ExtractionDecorator($container[Template::class], $container['property_group_enabled']),
                    new HydrationDecorator($container[Template::class], $container['property_group_enabled']),
                    new NewInstanceMethodDecorator($container[Template::class]),
                    new ClassMetadataDecorator(),
                ]
            );

            if ($container['property_group_enabled']) {
                $generator->addDecorator(new ExposedKeysDecorator());
            }

            return $generator;
        };

        $container[Template::class] = function ($container) {
            return new Template($container[Configuration::class]);
        };

        $container[HydratorCodeWriter::class] = function ($container) {
            $container[Filesystem::class]->mkdir($container['hydrator_dir']);

            if (!\is_writable($container['hydrator_dir'])) {
                throw new FilesystemException(\sprintf('The hydrator directory "%s" is not writable.', $container['hydrator_dir']));
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
