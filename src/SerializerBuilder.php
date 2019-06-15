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

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\Reader;
use Metadata\Cache\CacheInterface;
use Metadata\Cache\FileCache;
use Metadata\Driver\DriverInterface;
use Pimple\Container;
use TSantos\Serializer\Encoder\EncoderInterface;
use TSantos\Serializer\EventDispatcher\EventDispatcherInterface;
use TSantos\Serializer\EventDispatcher\EventSubscriberInterface;
use TSantos\Serializer\Exception\FilesystemException;
use TSantos\Serializer\Exception\MissingDependencyException;
use TSantos\Serializer\Metadata\ConfiguratorInterface;
use TSantos\Serializer\Metadata\Driver\AnnotationDriver;
use TSantos\Serializer\Normalizer\CollectionNormalizer;
use TSantos\Serializer\Normalizer\JsonNormalizer;
use TSantos\Serializer\ObjectInstantiator\ObjectInstantiatorInterface;

/**
 * Class SerializerBuilder.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class SerializerBuilder
{
    /**
     * @var Container
     */
    private $container;

    /**
     * SerializerBuilder constructor.
     *
     * @param Container|null $container
     * @param bool           $build
     */
    public function __construct(Container $container = null, bool $build = true)
    {
        $container = $container ?? new Container();

        if ($build) {
            $builder = require __DIR__.'/DependencyInjection/Pimple/Builder.php';
            $builder($container);
        }

        $this->container = $container;
    }

    /**
     * @param DriverInterface $driver
     *
     * @return SerializerBuilder
     */
    public function setMetadataDriver(DriverInterface $driver): self
    {
        $this->container['custom_metadata_driver'] = function () use ($driver) {
            return $driver;
        };

        return $this;
    }

    public function setMetadataDirs(array $dirs): self
    {
        $this->container['metadata_dirs'] = function () {
            return [];
        };

        $this->addMetadataDirs($dirs);

        return $this;
    }

    public function addMetadataDirs(array $dirs): self
    {
        foreach ($dirs as $namespace => $dir) {
            $this->addMetadataDir($namespace, $dir);
        }

        return $this;
    }

    public function addMetadataDir(string $namespace, string $dir): self
    {
        if (!\is_dir($dir)) {
            throw new FilesystemException('The metadata directory "'.$dir.'" does not exist');
        }

        $this->container->extend('metadata_dirs', function (array $dirs) use ($namespace, $dir) {
            $dirs[$namespace] = $dir;

            return $dirs;
        });

        return $this;
    }

    public function setHydratorDir(string $dir): self
    {
        $this->container['hydrator_dir'] = $dir;

        return $this;
    }

    public function setDebug(bool $debug): self
    {
        $this->container['debug'] = $debug;

        return $this;
    }

    public function addNormalizer($normalizer): self
    {
        $this->container->extend('normalizers', function (array $normalizers) use ($normalizer) {
            $normalizers[] = $normalizer;

            return $normalizers;
        });

        return $this;
    }

    public function enableBuiltInNormalizers(): self
    {
        $this->addNormalizer(new CollectionNormalizer());
        $this->addNormalizer(new JsonNormalizer());

        return $this;
    }

    public function addEncoder(EncoderInterface $encoder): self
    {
        $this->container->extend(EncoderRegistryInterface::class, function (EncoderRegistryInterface $encoders) use ($encoder) {
            return $encoders->add($encoder);
        });

        return $this;
    }

    public function addMetadataConfigurator(ConfiguratorInterface $configurator): self
    {
        $this->container->extend('metadata_configurators', function (array $configurators) use ($configurator) {
            $configurators[] = $configurator;

            return $configurators;
        });

        return $this;
    }

    public function setMetadataCacheDir(string $dir): self
    {
        if (!\is_dir($dir)) {
            throw new FilesystemException('The metadata cache directory "'.$dir.'" does not exist');
        }

        $this->setMetadataCache(new FileCache($dir));

        return $this;
    }

    public function setMetadataCache(CacheInterface $cache): self
    {
        $this->container[CacheInterface::class] = function () use ($cache) {
            return $cache;
        };

        return $this;
    }

    public function setHydratorGenerationStrategy(int $strategy): self
    {
        $this->container['generation_strategy'] = $strategy;

        return $this;
    }

    public function enableAnnotations(AnnotationReader $reader = null)
    {
        if (!\class_exists(AnnotationReader::class)) {
            throw new MissingDependencyException('The annotation reader was not loaded. '.
                'You must include the package doctrine/annotations as your composer dependency.');
        }

        $this->container['custom_metadata_driver'] = function ($container) use ($reader) {
            AnnotationRegistry::registerLoader('class_exists');

            return new AnnotationDriver($reader ?? $container[Reader::class]);
        };

        return $this;
    }

    public function setObjectInstantiator(ObjectInstantiatorInterface $instantiator): self
    {
        $this->container['custom_object_instantiator'] = function () use ($instantiator) {
            return $instantiator;
        };

        return $this;
    }

    public function addListener(string $eventName, callable $listener, int $priority = 0, string $type = null)
    {
        $this->container->extend(EventDispatcherInterface::class, function (EventDispatcherInterface $dispatcher) use ($eventName, $listener, $priority, $type) {
            $dispatcher->addListener($eventName, $listener, $priority, $type);

            return $dispatcher;
        });

        $this->container['has_listener'] = true;

        return $this;
    }

    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->container->extend(EventDispatcherInterface::class, function (EventDispatcherInterface $dispatcher) use ($subscriber) {
            $dispatcher->addSubscriber($subscriber);

            return $dispatcher;
        });

        $this->container['has_listener'] = true;

        return $this;
    }

    /**
     * @param string $format
     *
     * @return SerializerBuilder
     */
    public function setFormat(string $format): self
    {
        $this->container['format'] = $format;

        return $this;
    }

    /**
     * @param callable $circularReferenceHandler
     *
     * @return SerializerBuilder
     */
    public function setCircularReferenceHandler(callable $circularReferenceHandler): self
    {
        $this->container['circular_reference_handler'] = $this->container->protect($circularReferenceHandler);

        return $this;
    }

    /**
     * Disable property grouping feature.
     *
     * @return SerializerBuilder
     */
    public function disablePropertyGrouping(): self
    {
        $this->container['property_group_enabled'] = false;

        return $this;
    }

    /**
     * Enable property grouping feature.
     *
     * @return SerializerBuilder
     */
    public function enablePropertyGrouping(): self
    {
        $this->container['property_group_enabled'] = true;

        return $this;
    }

    /**
     * Enable max depth check feature.
     *
     * @return SerializerBuilder
     */
    public function enableMaxDepthCheck(): self
    {
        $this->container['max_depth_check_enabled'] = true;

        return $this;
    }

    /**
     * Disable max depth check feature.
     *
     * @return SerializerBuilder
     */
    public function disableMaxDepthCheck(): self
    {
        $this->container['max_depth_check_enabled'] = false;

        return $this;
    }

    /**
     * @return SerializerInterface
     */
    public function build(): SerializerInterface
    {
        return $this->container[SerializerInterface::class];
    }
}
