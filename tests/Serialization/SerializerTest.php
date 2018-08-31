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

namespace Tests\TSantos\Serializer\Serialization;

use Tests\TSantos\Serializer\Fixture\Model\Dummy;
use Tests\TSantos\Serializer\Fixture\Model\DummyAbstract;
use Tests\TSantos\Serializer\Fixture\Model\DummyInner;
use Tests\TSantos\Serializer\Fixture\Model\Inheritance\AbstractVehicle;
use Tests\TSantos\Serializer\Fixture\Model\Inheritance\Airplane;
use Tests\TSantos\Serializer\Fixture\Model\Inheritance\Car;
use Tests\TSantos\Serializer\Fixture\Model\Person;
use Tests\TSantos\Serializer\SerializerTestCase;
use TSantos\Serializer\Metadata\Driver\ReflectionDriver;

/** @runTestsInSeparateProcesses */
class SerializerTest extends SerializerTestCase
{
    /** @test */
    public function it_can_serialize_an_object_containing_scalar_value_with_getter()
    {
        $serializer = $this->createSerializer($this->createMapping(Dummy::class, [
            'foo' => ['type' => 'integer'],
        ]));

        $dummy = new Dummy(10);

        $this->assertSame('{"foo":10}', $serializer->serialize($dummy));
    }

    /** @test */
    public function it_can_serialize_an_object_containing_scalar_value_with_reader_filter_and_getter()
    {
        $serializer = $this->createSerializer($this->createMapping(Dummy::class, [
            'foo' => ['type' => 'string', 'readValueFilter' => 'trim($value)'],
        ]));

        $dummy = new Dummy(' FOO ');

        $this->assertSame('{"foo":"FOO"}', $serializer->serialize($dummy));
    }

    /** @test */
    public function it_can_serialize_an_object_containing_complex_value_and_getter()
    {
        $serializer = $this->createSerializer(array_merge(
            $this->createMapping(Dummy::class, [
                'foo' => ['type' => DummyInner::class],
            ]),
            $this->createMapping(DummyInner::class, [
                'baz' => ['type' => 'string'],
            ])
        ));

        $dummy = new Dummy(new DummyInner('inner'));

        $this->assertSame('{"foo":{"baz":"inner"}}', $serializer->serialize($dummy));
    }

    /** @test */
    public function it_can_serialize_an_object_containing_scalar_value_with_reflection()
    {
        $serializer = $this->createSerializer($this->createMapping(Dummy::class, [
            'bar' => ['type' => 'integer'],
        ]));

        $dummy = new Dummy(null, 10);

        $this->assertSame('{"bar":10}', $serializer->serialize($dummy));
    }

    /** @test */
    public function it_can_serialize_an_object_containing_scalar_value_with_reader_filter_and_reflection()
    {
        $serializer = $this->createSerializer($this->createMapping(Dummy::class, [
            'bar' => ['type' => 'string', 'readValueFilter' => 'trim($value)'],
        ]));

        $dummy = new Dummy(null, ' FOO ');

        $this->assertSame('{"bar":"FOO"}', $serializer->serialize($dummy));
    }

    /** @test */
    public function it_can_serialize_an_object_containing_complex_value_and_reflection()
    {
        $serializer = $this->createSerializer(array_merge(
            $this->createMapping(Dummy::class, [
                'bar' => ['type' => DummyInner::class],
            ]),
            $this->createMapping(DummyInner::class, [
                'baz' => ['type' => 'string'],
            ])
        ));

        $dummy = new Dummy(null, new DummyInner('inner'));

        $this->assertSame('{"bar":{"baz":"inner"}}', $serializer->serialize($dummy));
    }

    /** @test */
    public function it_can_serialize_a_simple_object_with_reflection(): void
    {
        $serializer = $this->createSerializer([
            Dummy::class => new ReflectionDriver(),
        ]);
        $json = $serializer->serialize(new Dummy('foo', 'bar'));
        $this->assertSame('{"foo":"foo","bar":"bar"}', $json);
    }

    /** @test */
    public function it_can_serialize_abstract_classes()
    {
        $serializer = $this->createSerializer(array_merge(
            $this->createMapping(
                DummyAbstract::class,
                [
                    'foobar' => [],
                ],
                [],
                [
                    'discriminatorMap' => [
                        'field' => 'type',
                        'mapping' => [
                            'dummy' => Dummy::class,
                            'inner' => DummyInner::class,
                        ],
                    ],
                ]
            ),
            $this->createMapping(Dummy::class, [
                'foo' => [],
            ]),
            $this->createMapping(DummyInner::class, [
                'bar' => [],
            ])
        ));

        $serialized = $serializer->serialize(new Dummy('foo', 'bar'));
        $this->assertSame('{"foobar":null,"foo":"foo","type":"dummy"}', $serialized);
    }

    /** @test */
    public function it_can_serialize_abstract_mixed_with_concrete_classes()
    {
        $serializer = $this->createSerializer(\array_merge(
            $this->createMapping(
                DummyAbstract::class,
                [
                    'foobar' => [],
                ],
                [],
                [
                    'discriminatorMap' => [
                        'field' => 'type',
                        'mapping' => [
                            'dummy' => Dummy::class,
                            'inner' => DummyInner::class,
                        ],
                    ],
                ]
            ),
            $this->createMapping(Dummy::class, [
                'foo' => ['type' => 'string'],
            ])
        ));

        $serialized = $serializer->serialize(new Dummy('foo'));
        $this->assertSame('{"foobar":null,"foo":"foo","type":"dummy"}', $serialized);
    }
}
