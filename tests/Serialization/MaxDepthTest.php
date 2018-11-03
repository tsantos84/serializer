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

use Tests\TSantos\Serializer\Fixture\Model\Dummy;
use Tests\TSantos\Serializer\Fixture\Model\DummyInner;
use Tests\TSantos\Serializer\Fixture\Model\DummyPublic;
use TSantos\Serializer\SerializationContext;

/**
 * Class MaxDepthTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 *
 * @runTestsInSeparateProcesses
 */
class MaxDepthTest extends SerializerTestCase
{
    protected function createBuilder()
    {
        return parent::createBuilder()->enableMaxDepthCheck();
    }

    /** @test */
    public function it_should_not_check_max_depth()
    {
        $serializer = $this->createSerializer(\array_merge(
            $this->createMapping(Dummy::class, [
                'foo' => ['type' => DummyInner::class, 'maxDepth' => 1],
            ]),
            $this->createMapping(DummyInner::class, [
                'baz' => ['type' => DummyPublic::class],
            ]),
            $this->createMapping(DummyPublic::class, [
                'bar' => ['type' => 'integer'],
            ])
        ));

        $public = new DummyPublic();
        $public->bar = 100;
        $inner = new DummyInner($public);
        $dummy = new Dummy($inner);

        $serialized = $serializer->serialize($dummy);

        $this->assertSame('{"foo":{"baz":{"bar":100}}}', $serialized);
    }

    /** @test */
    public function it_should_not_serialize_if_max_depth_is_achieved()
    {
        $serializer = $this->createSerializer(\array_merge(
            $this->createMapping(Dummy::class, [
                'foo' => ['type' => DummyInner::class, 'maxDepth' => 1],
            ]),
            $this->createMapping(DummyInner::class, [
                'baz' => ['type' => DummyPublic::class],
            ]),
            $this->createMapping(DummyPublic::class, [
                'bar' => ['type' => 'integer'],
            ])
        ));

        $public = new DummyPublic();
        $public->bar = 100;
        $inner = new DummyInner($public);
        $dummy = new Dummy($inner);

        $context = new SerializationContext();
        $context->enableMaxDepthCheck();

        $serialized = $serializer->serialize($dummy, $context);

        $this->assertSame('{"foo":{"baz":null}}', $serialized);
    }
}
