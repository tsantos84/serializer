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

namespace Tests\TSantos\Serializer\Deserialization;

use Tests\TSantos\Serializer\Fixture\Model\Dummy;
use Tests\TSantos\Serializer\Fixture\Model\DummyInner;
use Tests\TSantos\Serializer\Fixture\Model\DummyPublic;
use Tests\TSantos\Serializer\SerializerTestCase;
use TSantos\Serializer\DeserializationContext;
use TSantos\Serializer\Metadata\Driver\ReflectionDriver;

/**
 * Class DeserializeObjectTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 * @runTestsInSeparateProcesses
 */
class DeserializeSimpleObjectTest extends SerializerTestCase
{
    protected $clearCache = false;

    /** @test */
    public function it_can_deserialize_an_object_with_value_filter_and_setter()
    {
        $serializer = $this->createSerializer($this->createMapping(Dummy::class, [
            'foo' => ['type' => 'string', 'writeValueFilter' => 'trim($value)'],
        ]));

        $dummy = $serializer->deserialize('{"foo":" Foobar "}', Dummy::class);

        $this->assertInstanceOf(Dummy::class, $dummy);
        $this->assertSame('Foobar', $dummy->getFoo());
    }

    /** @test */
    public function it_can_deserialize_an_object_containing_scalar_value_with_setter()
    {
        $serializer = $this->createSerializer($this->createMapping(Dummy::class, [
            'foo' => ['type' => 'string'],
        ]));

        $dummy = $serializer->deserialize('{"foo":"Foobar"}', Dummy::class);

        $this->assertInstanceOf(Dummy::class, $dummy);
        $this->assertSame('Foobar', $dummy->getFoo());
    }

    /** @test */
    public function it_can_deserialize_an_object_containing_a_complex_value_with_setter()
    {
        $serializer = $this->createSerializer(\array_merge(
            $this->createMapping(Dummy::class, [
                'foo' => ['type' => DummyInner::class],
            ]),
            $this->createMapping(DummyInner::class, [
                'baz' => ['type' => 'string'],
            ])
        ));

        $dummy = $serializer->deserialize('{"foo":{"baz":"baz"}}', Dummy::class);

        $this->assertInstanceOf(Dummy::class, $dummy);
        $this->assertInstanceOf(DummyInner::class, $dummy->getFoo());
        $this->assertSame('baz', $dummy->getFoo()->getBaz());
    }

    /** @test */
    public function it_can_deserialize_an_object_with_value_filter_and_reflection()
    {
        $serializer = $this->createSerializer($this->createMapping(Dummy::class, [
            'bar' => ['type' => 'string', 'writeValueFilter' => 'trim($value)'],
        ]));

        $dummy = $serializer->deserialize('{"bar":" Foobar "}', Dummy::class);

        $ref = new \ReflectionObject($dummy);
        $prop = $ref->getProperty('bar');
        $prop->setAccessible(true);

        $this->assertInstanceOf(Dummy::class, $dummy);
        $this->assertSame('Foobar', $prop->getValue($dummy));
    }

    /** @test */
    public function it_can_deserialize_an_object_containing_scalar_value_with_reflection()
    {
        $serializer = $this->createSerializer($this->createMapping(Dummy::class, [
            'bar' => ['type' => 'string'],
        ]));

        $dummy = $serializer->deserialize('{"bar":"Foobar"}', Dummy::class);

        $ref = new \ReflectionObject($dummy);
        $prop = $ref->getProperty('bar');
        $prop->setAccessible(true);

        $this->assertInstanceOf(Dummy::class, $dummy);
        $this->assertSame('Foobar', $prop->getValue($dummy));
    }

    /** @test */
    public function it_can_deserialize_an_object_containing_a_complex_value_with_reflection()
    {
        $serializer = $this->createSerializer(\array_merge(
            $this->createMapping(Dummy::class, [
                'bar' => ['type' => DummyInner::class],
            ]),
            $this->createMapping(DummyInner::class, [
                'baz' => ['type' => 'string'],
            ])
        ));

        $dummy = $serializer->deserialize('{"bar":{"baz":"baz"}}', Dummy::class);

        $ref = new \ReflectionObject($dummy);
        $prop = $ref->getProperty('bar');
        $prop->setAccessible(true);

        $this->assertInstanceOf(Dummy::class, $dummy);
        $this->assertInstanceOf(DummyInner::class, $prop->getValue($dummy));
        $this->assertSame('baz', $prop->getValue($dummy)->getBaz());
    }

    /** @test */
    public function it_can_deserialize_a_simple_object_by_reflection()
    {
        $serializer = $this->createSerializer([
            Dummy::class => new ReflectionDriver(),
        ]);

        $content = '{"foo":"bar"}';

        $dummy = $serializer->deserialize($content, Dummy::class);

        $this->assertSame('bar', $dummy->getFoo());
    }

    /** @test */
    public function it_cannot_deserialize_read_only_attributes()
    {
        $serializer = $this->createSerializer($this->createMapping(Dummy::class, [
            'foo' => ['type' => 'integer', 'readOnly' => true],
            'bar' => ['type' => 'string'],
        ]));

        $dummy = new Dummy(100);

        $dummy = $serializer->deserialize(
            '{"foo":200,"bar":"bar"}',
            Dummy::class,
            DeserializationContext::create()->setTarget($dummy)
        );

        $this->assertSame(100, $dummy->getFoo());
    }

    /** @test */
    public function it_can_deserialize_object_with_public_attributes()
    {
        $serializer = $this->createSerializer($this->createMapping(DummyPublic::class, [
            'foo' => ['type' => 'integer'],
            'bar' => ['type' => 'string'],
        ]));

        $dummy = $serializer->deserialize(
            '{"foo":200,"bar":"bar"}',
            DummyPublic::class
        );

        $this->assertSame(200, $dummy->foo);
        $this->assertSame('bar', $dummy->bar);
    }
}
