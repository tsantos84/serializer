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
use Tests\TSantos\Serializer\Fixture\Model\DummyAbstract;
use Tests\TSantos\Serializer\Fixture\Model\DummyInner;
use Tests\TSantos\Serializer\Fixture\Model\DummyInterface;
use Tests\TSantos\Serializer\SerializerTestCase;

/**
 * Class DeserializeAbstractClassTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 *
 * @runTestsInSeparateProcessesxxxx
 */
class InheritanceDeserializationTest extends SerializerTestCase
{
    /** @test */
    public function it_can_deserialize_abstract_classes()
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
                        'field' => 'customField',
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
                'baz' => [],
            ])
        ));

        $dummy = $serializer->deserialize('{"foo":"foo","customField":"dummy","foobar":"foobar"}', DummyAbstract::class);
        $this->assertInstanceOf(Dummy::class, $dummy);
        $this->assertSame('foo', $dummy->getFoo());
        $this->assertSame('foobar', $dummy->getFoobar());

        $inner = $serializer->deserialize('{"baz":"baz","customField":"inner","foobar":"foobar"}', DummyAbstract::class);
        $this->assertInstanceOf(DummyInner::class, $inner);
        $this->assertSame('baz', $inner->getBaz());
        $this->assertSame('foobar', $inner->getFoobar());
    }

    /** @test */
    public function it_can_deserialize_interfaces()
    {
        $serializer = $this->createSerializer(\array_merge(
            $this->createMapping(DummyInterface::class, [], [], [
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

        $dummy = $serializer->deserialize('{"foo":"foo","customField":"dummy"}', DummyInterface::class);
        $this->assertInstanceOf(Dummy::class, $dummy);
        $this->assertSame('foo', $dummy->getFoo());

        $inner = $serializer->deserialize('{"baz":"baz","customField":"inner"}', DummyInterface::class);
        $this->assertInstanceOf(DummyInner::class, $inner);
        $this->assertSame('baz', $inner->getBaz());
    }
}
