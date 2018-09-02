<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\TSantos\Serializer\Serialization;

use Tests\TSantos\Serializer\Fixture\Model\Dummy;
use Tests\TSantos\Serializer\Fixture\Model\DummyInner;
use Tests\TSantos\Serializer\SerializerTestCase;


/**
 * Class SerializeCollectionTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 * @runTestsInSeparateProcesses
 */
class SerializeCollectionTest extends SerializerTestCase
{
    /** @test */
    public function it_can_serialize_a_collection_of_simple_objects()
    {
        $serializer = $this->createSerializer($this->createMapping(Dummy::class, [
            'foo' => ['type' => 'integer'],
        ]));

        $dummies = [new Dummy(10), new Dummy(20), new Dummy(30)];

        $this->assertSame('[{"foo":10},{"foo":20},{"foo":30}]', $serializer->serialize($dummies));
    }

    /** @test */
    public function it_can_serialize_an_object_containing_a_collection_of_mixed_value_types()
    {
        $serializer = $this->createSerializer($this->createMapping(Dummy::class, [
            'foo' => ['type' => 'mixed[]'],
        ]));

        $dummy = new Dummy(['1', 2, 3.5, ['foo' => 'bar']]);

        $this->assertSame('{"foo":["1",2,3.5,{"foo":"bar"}]}', $serializer->serialize($dummy));
    }

    /** @test */
    public function it_can_serialize_an_object_containing_a_collection_of_scalar_values()
    {
        $serializer = $this->createSerializer($this->createMapping(Dummy::class, [
            'foo' => ['type' => 'string[]'],
        ]));

        $dummy = new Dummy(['1', 2, 3.5]);

        $this->assertSame('{"foo":["1","2","3.5"]}', $serializer->serialize($dummy));
    }

    /** @test */
    public function it_can_serialize_an_object_containing_a_collection_of_complex_values()
    {
        $serializer = $this->createSerializer(array_merge(
            $this->createMapping(Dummy::class, [
                'foo' => ['type' => DummyInner::class . '[]'],
            ]),
            $this->createMapping(DummyInner::class, [
                'baz' => [],
            ])
        ));

        $dummy = new Dummy([new DummyInner('baz2'), new DummyInner('baz2')]);

        $this->assertSame('{"foo":[{"baz":"baz2"},{"baz":"baz2"}]}', $serializer->serialize($dummy));
    }
}
