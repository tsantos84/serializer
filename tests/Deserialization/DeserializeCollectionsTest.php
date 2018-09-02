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
use Tests\TSantos\Serializer\Fixture\Model\Person;
use Tests\TSantos\Serializer\SerializerTestCase;

/**
 * Class DeserializeArrayOfObjectTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 * @runTestsInSeparateProcesses
 */
class DeserializeCollectionsTest extends SerializerTestCase
{
    /** @test */
    public function it_can_deserialize_a_collection_of_values_using_writer_filter_and_setter()
    {
        $serializer = $this->createSerializer($this->createMapping(Dummy::class, [
            'foo' => ['type' => 'integer[]', 'writeValueFilter' => 'array_filter($value)'],
        ]));

        $dummy = $serializer->deserialize('{"foo":[null,2,3,null]}', Dummy::class);
        $this->assertSame([1 => 2, 2 => 3], $dummy->getFoo());
    }

    /** @test */
    public function it_can_deserialize_a_collection_of_scalar_values_through_setter()
    {
        $serializer = $this->createSerializer($this->createMapping(Dummy::class, [
            'foo' => ['type' => 'integer[]'],
        ]));

        $dummy = $serializer->deserialize('{"foo":[1,2,3,4,5,6,7,8,9,10]}', Dummy::class);
        $this->assertSame([1, 2, 3, 4, 5, 6, 7, 8, 9, 10], $dummy->getFoo());
    }

    /** @test */
    public function it_can_deserialize_a_collection_of_mixed_values_through_and_setter()
    {
        $serializer = $this->createSerializer($this->createMapping(Dummy::class, [
            'foo' => ['type' => 'mixed[]'],
        ]));

        $dummy = $serializer->deserialize('{"foo":[1,2,"3",4,5,6,"7",8,9,10]}', Dummy::class);
        $this->assertSame([1, 2, '3', 4, 5, 6, '7', 8, 9, 10], $dummy->getFoo());
    }

    /** @test */
    public function it_can_deserialize_a_collection_of_persons_through_and_setter()
    {
        $serializer = $this->createSerializer(\array_merge(
            $this->createMapping(Dummy::class, [
                'foo' => ['type' => Person::class.'[]'],
            ]),
            $this->createMapping(Person::class, [
                'name' => [],
            ])
        ));

        $dummy = $serializer->deserialize('{"foo":[{"name":"Tales"}]}', Dummy::class);
        $this->assertCount(1, $dummy->getFoo());
        $this->assertInstanceOf(Person::class, $dummy->getFoo()[0]);
        $this->assertSame($dummy->getFoo()[0]->getName(), 'Tales');
    }

    /** @test */
    public function it_can_deserialize_a_collection_of_values_using_writer_filter_and_reflection()
    {
        $serializer = $this->createSerializer($this->createMapping(Dummy::class, [
            'bar' => ['type' => 'integer[]', 'writeValueFilter' => 'array_filter($value)'],
        ]));

        $dummy = $serializer->deserialize('{"bar":[null,2,3,null]}', Dummy::class);

        $ref = new \ReflectionObject($dummy);
        $prop = $ref->getProperty('bar');
        $prop->setAccessible(true);

        $this->assertSame([1 => 2, 2 => 3], $prop->getValue($dummy));
    }

    /** @test */
    public function it_can_deserialize_a_collection_of_scalar_values_through_reflection()
    {
        $serializer = $this->createSerializer($this->createMapping(Dummy::class, [
            'bar' => ['type' => 'integer[]'],
        ]));

        $dummy = $serializer->deserialize('{"bar":[1,2,3,4,5,6,7,8,9,10]}', Dummy::class);

        $ref = new \ReflectionObject($dummy);
        $prop = $ref->getProperty('bar');
        $prop->setAccessible(true);

        $this->assertSame([1, 2, 3, 4, 5, 6, 7, 8, 9, 10], $prop->getValue($dummy));
    }

    /** @test */
    public function it_can_deserialize_a_collection_of_mixed_values_through_reflection()
    {
        $serializer = $this->createSerializer($this->createMapping(Dummy::class, [
            'bar' => ['type' => 'mixed[]'],
        ]));

        $dummy = $serializer->deserialize('{"bar":[1,2,"3",4,5,6,"7",8,9,10]}', Dummy::class);

        $ref = new \ReflectionObject($dummy);
        $prop = $ref->getProperty('bar');
        $prop->setAccessible(true);

        $this->assertSame([1, 2, '3', 4, 5, 6, '7', 8, 9, 10], $prop->getValue($dummy));
    }

    /** @test */
    public function it_can_deserialize_a_collection_of_persons_through_reflection()
    {
        $serializer = $this->createSerializer(\array_merge(
            $this->createMapping(Dummy::class, [
                'bar' => ['type' => Person::class.'[]'],
            ]),
            $this->createMapping(Person::class, [
                'name' => [],
            ])
        ));

        $dummy = $serializer->deserialize('{"bar":[{"name":"Tales"}]}', Dummy::class);

        $ref = new \ReflectionObject($dummy);
        $prop = $ref->getProperty('bar');
        $prop->setAccessible(true);

        $this->assertCount(1, $prop->getValue($dummy));
        $this->assertInstanceOf(Person::class, $prop->getValue($dummy)[0]);
        $this->assertSame($prop->getValue($dummy)[0]->getName(), 'Tales');
    }

    /** @test */
    public function it_can_deserialize_an_array_of_integers()
    {
        $serializer = $this->createSerializer();
        $content = '[1,2,3,4,5,6,7,8,9,10]';
        $collection = $serializer->deserialize($content, 'integer[]');
        $this->assertSame([1, 2, 3, 4, 5, 6, 7, 8, 9, 10], $collection);
    }

    /** @test */
    public function it_can_deserialize_an_untyped_array_and_keep_its_items_data_type()
    {
        $serializer = $this->createSerializer();
        $content = '[1,2,"3",4,5,6,7,8,9,10]';
        $collection = $serializer->deserialize($content, 'mixed[]');
        $this->assertSame([1, 2, '3', 4, 5, 6, 7, 8, 9, 10], $collection);
    }

    /** @test */
    public function it_can_deserialize_an_array_of_strings()
    {
        $serializer = $this->createSerializer();
        $content = '[1,2,3,4,5,6,7,8,9,10]';
        $collection = $serializer->deserialize($content, 'string[]');
        $this->assertSame(['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'], $collection);
    }

    /** @test */
    public function it_can_deserialize_an_array_of_float()
    {
        $serializer = $this->createSerializer();
        $content = '[1.1,2.2,3.3,4.4,5.5,6.6,7.7,8.8,9.9,10.11]';
        $collection = $serializer->deserialize($content, 'float[]');
        $this->assertSame([1.1, 2.2, 3.3, 4.4, 5.5, 6.6, 7.7, 8.8, 9.9, 10.11], $collection);
    }
}
