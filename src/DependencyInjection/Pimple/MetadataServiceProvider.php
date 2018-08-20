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

namespace TSantos\Serializer\DependencyInjection\Pimple;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Metadata\Cache\CacheInterface;
use Metadata\Driver\DriverChain;
use Metadata\Driver\DriverInterface;
use Metadata\Driver\FileLocator;
use Metadata\MetadataFactory;
use Metadata\MetadataFactoryInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use TSantos\Serializer\Metadata\Configurator\DateTimeConfigurator;
use TSantos\Serializer\Metadata\Configurator\GetterConfigurator;
use TSantos\Serializer\Metadata\Configurator\PropertyTypeConfigurator;
use TSantos\Serializer\Metadata\Configurator\SetterConfigurator;
use TSantos\Serializer\Metadata\Configurator\VirtualPropertyTypeConfigurator;
use TSantos\Serializer\Metadata\Driver\ConfiguratorDriver;
use TSantos\Serializer\Metadata\Driver\ReflectionDriver;
use TSantos\Serializer\Metadata\Driver\XmlDriver;
use TSantos\Serializer\Metadata\Driver\YamlDriver;

/**
 * Class MetadataServiceProvider.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class MetadataServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['metadata_cache_dir'] = null;

        $container['metadata_dirs'] = function () {
            return [];
        };

        $container[MetadataFactoryInterface::class] = function ($container) {
            return new MetadataFactory(
                $container[DriverInterface::class],
                'Metadata\ClassHierarchyMetadata',
                $container['debug']
            );
        };

        $container->extend(MetadataFactoryInterface::class, function (MetadataFactoryInterface $factory, $container) {
            if (isset($container[CacheInterface::class])) {
                $factory->setCache($container[CacheInterface::class]);
            }

            return $factory;
        });

        $container[FileLocator::class] = function ($container) {
            return new FileLocator($container['metadata_dirs']);
        };

        $container[DriverChain::class] = function ($container) {
            return new DriverChain([
                new YamlDriver($container[FileLocator::class]),
                new XmlDriver($container[FileLocator::class]),
                new ReflectionDriver(),
            ]);
        };

        $container[DriverInterface::class] = function ($container) {
            $driver = $container[DriverChain::class];

            if (isset($container['custom_metadata_driver'])) {
                $driver = $container['custom_metadata_driver'];
            }

            return new ConfiguratorDriver($driver, [
                new PropertyTypeConfigurator($container[PropertyInfoExtractorInterface::class]),
                new VirtualPropertyTypeConfigurator(),
                new GetterConfigurator(),
                new SetterConfigurator(),
                new DateTimeConfigurator(),
            ]);
        };

        if (\class_exists(AnnotationReader::class)) {
            $container[Reader::class] = function () {
                return new AnnotationReader();
            };
        }

        $container[PropertyInfoExtractorInterface::class] = function ($container) {
            return new PropertyInfoExtractor([], [
                new ReflectionExtractor(),
                new PhpDocExtractor(),
            ]);
        };
    }
}
