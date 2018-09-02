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

namespace Tests\Serializer;

use Tests\TSantos\Serializer\Fixture\Model\Dummy;
use Tests\TSantos\Serializer\Fixture\Model\DummyAbstract;
use Tests\TSantos\Serializer\Fixture\Model\DummyInner;
use Tests\TSantos\Serializer\SerializerTestCase;

/**
 * Class InheritanceSerializationTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 * @runTestsInSeparateProcesses
 */
class InheritanceSerializationTest extends SerializerTestCase
{
    protected $clearCache = false;

    /** @test */
    public function it_can_serialize_abstract_classes_accessing_data_through_getter()
    {
        $serializer = $this->createSerializer(array_merge(
            $this->createMapping(DummyAbstract::class, ['foobar' => []],[],[
                'discriminatorMap' => [
                    'field' => 'customField',
                    'mapping' => [
                        'dummy' => Dummy::class,
                        'inner' => DummyInner::class,
                    ],
                ],
            ]),
            $this->createMapping(Dummy::class, [
                'foo' => [],
            ]),
            $this->createMapping(DummyInner::class, [
                'baz' => [],
            ])
        ));

        $dummy = new Dummy('foo', 'bar');
        $dummy->setFoobar('foobar');
        $this->assertSame('{"foobar":"foobar","foo":"foo","customField":"dummy"}', $serializer->serialize($dummy));

        $dummyInner = new DummyInner('baz');
        $dummyInner->setFoobar('foobarInner');
        $this->assertSame('{"foobar":"foobarInner","baz":"baz","customField":"inner"}', $serializer->serialize($dummyInner));
    }

    /** @test */
    public function it_can_serialize_abstract_classes_accessing_data_through_reflection()
    {
        $serializer = $this->createSerializer(array_merge(
            $this->createMapping(DummyAbstract::class, ['foo' => []],[],[
                'discriminatorMap' => [
                    'field' => 'customField',
                    'mapping' => [
                        'dummy' => Dummy::class,
                        'inner' => DummyInner::class,
                    ],
                ],
            ]),
            $this->createMapping(Dummy::class, [
                'bar' => [],
            ]),
            $this->createMapping(DummyInner::class, [
                'qux' => [],
            ])
        ));

        $dummy = new Dummy(null, 'bar');
        $this->assertSame('{"foo":null,"bar":"bar","customField":"dummy"}', $serializer->serialize($dummy));

        $dummyInner = new DummyInner(null, 'qux');
        $this->assertSame('{"foo":null,"qux":"qux","customField":"inner"}', $serializer->serialize($dummyInner));
    }
}
