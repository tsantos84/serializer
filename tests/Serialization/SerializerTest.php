<?php

declare(strict_types=1);
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
use Tests\TSantos\Serializer\Fixture\Model\Person;
use Tests\TSantos\Serializer\SerializerTestCase;
use TSantos\Serializer\Metadata\Driver\ReflectionDriver;

/** @runTestsInSeparateProcesses */
class SerializerTest extends SerializerTestCase
{
    /** @test */
    public function it_can_serialize_a_collection_of_simple_objects()
    {
        $serializer = $this->createSerializer($this->createMapping(Person::class, [
            'id' => ['type' => 'integer'],
            'name' => [],
            'married' => ['type' => 'boolean', 'getter' => 'isMarried'],
        ]));

        $persons = [$this->createPerson(), $this->createPerson(), $this->createPerson()];

        $json = $serializer->serialize($persons);

        $this->assertEquals(json_encode([
            [
                'id' => 1,
                'name' => 'Tales',
                'married' => true,
            ],
            [
                'id' => 1,
                'name' => 'Tales',
                'married' => true,
            ],
            [
                'id' => 1,
                'name' => 'Tales',
                'married' => true,
            ],
        ]), $json);
    }

    /** @test */
    public function it_can_serialize_a_simple_object()
    {
        $serializer = $this->createSerializer($this->createMapping(Person::class, [
            'id' => ['type' => 'integer'],
            'name' => [],
            'married' => ['type' => 'boolean', 'getter' => 'isMarried'],
        ]));

        $person = $this->createPerson();

        $json = $serializer->serialize($person);

        $this->assertEquals(json_encode([
            'id' => 1,
            'name' => 'Tales',
            'married' => true,
        ]), $json);
    }

    /** @test */
    public function it_can_serialize_a_simple_object_with_reflection(): void
    {
        $serializer = $this->createSerializer([
            Dummy::class => new ReflectionDriver(),
        ]);
        $json = $serializer->serialize(new Dummy('bar'));
        $this->assertEquals('{"foo":"bar"}', $json);
    }

    private function createPerson()
    {
        $person = new Person(1, 'Tales', true);
//        $person->setLastName('Santos');

        return $person;
    }
}
