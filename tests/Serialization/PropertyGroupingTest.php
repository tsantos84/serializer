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
use Tests\TSantos\Serializer\SerializerTestCase;
use TSantos\Serializer\SerializationContext;

/**
 * Class PropertyGroupingTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 *
 * @runTestsInSeparateProcesses
 */
class PropertyGroupingTest extends SerializerTestCase
{
    protected function createBuilder()
    {
        $builder = parent::createBuilder();
        $builder->enablePropertyGrouping();
        return $builder;
    }

    /** @test */
    public function it_should_serialize_only_properties_specified_in_group_list()
    {
        $serializer = $this->createSerializer($this->createMapping(Dummy::class, [
            'foo' => ['groups' => ['v1']],
            'bar' => ['groups' => ['v2']],
        ]));

        $person = new Dummy('foo', 'bar');

        $context = SerializationContext::create()->setGroups(['v1']);

        $this->assertSame('{"foo":"foo"}', $serializer->serialize($person, $context));
    }

    /** @test */
    public function it_should_serialize_only_properties_specified_in_group_list_and_default()
    {
        $serializer = $this->createSerializer($this->createMapping(Dummy::class, [
            'foo' => ['groups' => ['v1']],
            'bar' => [],
        ]));

        $person = new Dummy('foo', 'bar');

        $context = SerializationContext::create()->setGroups(['v1', 'Default']);

        $this->assertSame('{"foo":"foo","bar":"bar"}', $serializer->serialize($person, $context));
    }

    /** @test */
    public function it_should_not_serialize_properties()
    {
        $serializer = $this->createSerializer($this->createMapping(Dummy::class, [
            'foo' => ['groups' => ['v1']],
            'bar' => ['groups' => ['v1']],
        ]));

        $person = new Dummy('foo', 'bar');

        $context = SerializationContext::create()->setGroups(['v0']);

        $this->assertSame('[]', $serializer->serialize($person, $context));
    }
}
