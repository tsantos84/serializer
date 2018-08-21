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

namespace Tests\TSantos\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Instantiator\Instantiator;
use Metadata\Cache\CacheInterface;
use Metadata\Cache\FileCache;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Tests\TSantos\Serializer\Fixture\DummyEventDispatcher;
use TSantos\Serializer\Encoder\JsonEncoder;
use TSantos\Serializer\EncoderRegistry;
use TSantos\Serializer\EncoderRegistryInterface;
use TSantos\Serializer\EventDispatcher\EventDispatcherInterface;
use TSantos\Serializer\EventDispatcher\EventSubscriberInterface;
use TSantos\Serializer\Events;
use TSantos\Serializer\HydratorCompiler;
use TSantos\Serializer\Metadata\Configurator\DateTimeConfigurator;
use TSantos\Serializer\Metadata\Driver\AnnotationDriver;
use TSantos\Serializer\Metadata\Driver\ReflectionDriver;
use TSantos\Serializer\Normalizer\JsonNormalizer;
use TSantos\Serializer\ObjectInstantiator\DoctrineInstantiator;
use TSantos\Serializer\SerializerBuilder;
use TSantos\Serializer\SerializerInterface;

/**
 * Class SerializerBuilderTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class SerializerBuilderTest extends TestCase
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var SerializerBuilder
     */
    private $builder;

    public function setUp()
    {
        $this->container = new Container();
        $this->builder = new SerializerBuilder($this->container);
    }

    /** @test */
    public function it_can_set_a_custom_metadata_driver()
    {
        $driver = new ReflectionDriver();
        $this->builder->setMetadataDriver($driver);
        $this->assertSame($driver, $this->container['custom_metadata_driver']);
    }

    /** @test */
    public function it_set_many_metadata_dirs_at_once()
    {
        $this->builder->setMetadataDirs(['My\\Namespace' => '/tmp']);
        $this->assertSame(['My\\Namespace' => '/tmp'], $this->container['metadata_dirs']);
    }

    /** @test */
    public function it_can_change_the_hydrator_dir()
    {
        $this->container['hydrator_dir'] = 'some/dir';
        $this->builder->setHydratorDir('/tmp');
        $this->assertSame('/tmp', $this->container['hydrator_dir']);
    }

    /** @test */
    public function it_can_change_the_debug_mode()
    {
        $this->container['debug'] = true;
        $this->builder->setDebug(false);
        $this->assertFalse($this->container['debug']);
    }

    /** @test */
    public function it_can_add_custom_normalizers()
    {
        $this->container['normalizers'] = function () {
            return [];
        };

        $this->builder->addNormalizer($normalizer = new JsonNormalizer());
        $this->assertCount(1, $this->container['normalizers']);
        $this->assertSame($normalizer, $this->container['normalizers'][0]);
    }

    /** @test */
    public function it_can_enable_the_built_in_normalizers()
    {
        $this->container['normalizers'] = function () {
            return [];
        };

        $this->builder->enableBuiltInNormalizers();
        $this->assertCount(2, $this->container['normalizers']);
    }

    /** @test */
    public function it_can_add_a_custom_encoder()
    {
        $this->container[EncoderRegistryInterface::class] = function () {
            return new EncoderRegistry();
        };

        $this->builder->addEncoder(new JsonEncoder());
        $this->assertTrue($this->container[EncoderRegistryInterface::class]->has('json'));
    }

    /** @test */
    public function it_can_add_custom_metadata_configurator()
    {
        $this->container['metadata_configurators'] = function () {
            return [];
        };

        $this->builder->addMetadataConfigurator(new DateTimeConfigurator());
        $this->assertCount(1, $this->container['metadata_configurators']);
    }

    /** @test */
    public function it_can_set_the_metadata_cache_dir()
    {
        $this->builder->setMetadataCacheDir('/tmp');
        $this->assertInstanceOf(FileCache::class, $this->container[CacheInterface::class]);
    }

    /** @test */
    public function it_change_the_hydrator_generation_strategy()
    {
        $this->builder->setHydratorGenerationStrategy(HydratorCompiler::AUTOGENERATE_ALWAYS);
        $this->assertSame(HydratorCompiler::AUTOGENERATE_ALWAYS, $this->container['generation_strategy']);
    }

    /** @test */
    public function it_can_enable_annotations_with_custom_reader_to_load_metadata()
    {
        $this->container[Reader::class] = function () {
            $this->fail('The built-in reader should not be used');
        };

        $reader = new AnnotationReader();
        $this->builder->enableAnnotations($reader);
        $this->assertInstanceOf(AnnotationDriver::class, $this->container['custom_metadata_driver']);
    }

    /** @test */
    public function it_can_enable_annotations_with_default_reader_to_load_metadata()
    {
        $this->container[Reader::class] = function () {
            return new AnnotationReader();
        };
        $this->builder->enableAnnotations();
        $this->assertInstanceOf(AnnotationDriver::class, $this->container['custom_metadata_driver']);
    }

    /** @test */
    public function it_can_change_the_default_object_instantiator()
    {
        $instantiator = new DoctrineInstantiator(new Instantiator());
        $this->builder->setObjectInstantiator($instantiator);
        $this->assertSame($instantiator, $this->container['custom_object_instantiator']);
    }

    /** @test */
    public function it_can_add_event_listeners()
    {
        $this->container[EventDispatcherInterface::class] = function () {
            return new DummyEventDispatcher();
        };

        $this->builder->addListener(Events::POST_SERIALIZATION, function () {}, 10, 'MyType');

        $this->assertTrue($this->container['has_listener']);
        $this->assertCount(1, $this->container[EventDispatcherInterface::class]->getListeners());
    }

    /** @test */
    public function it_can_event_subscriber()
    {
        $this->container[EventDispatcherInterface::class] = function () {
            return new DummyEventDispatcher();
        };

        $subscriber = new class() implements EventSubscriberInterface {
            public static function getListeners(): array
            {
                return [];
            }
        };

        $this->builder->addSubscriber($subscriber);

        $this->assertTrue($this->container['has_listener']);
        $this->assertCount(1, $this->container[EventDispatcherInterface::class]->getSubscribers());
    }

    /** @test */
    public function it_can_change_the_serialization_format()
    {
        $this->builder->setFormat('json');
        $this->assertSame('json', $this->container['format']);
    }

    /** @test */
    public function it_can_set_the_circular_reference_handler()
    {
        $handler = function () {
            return 'foo';
        };

        $this->builder->setCircularReferenceHandler($handler);
        $this->assertSame('foo', $this->container['circular_reference_handler']());
    }

    /** @test */
    public function it_can_build_the_serializer_service()
    {
        $serializer = $this->createMock(SerializerInterface::class);

        $this->container[SerializerInterface::class] = function () use ($serializer) {
            return $serializer;
        };

        $this->assertSame($serializer, $this->builder->build());
    }
}
