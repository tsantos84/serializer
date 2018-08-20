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
    public function it_can_serialize_a_collection_of_simple_objects()
    {
        $serializer = $this->createSerializer($this->createMapping(Person::class, [
            'id' => ['type' => 'integer'],
            'name' => [],
            'married' => ['type' => 'boolean', 'getter' => 'isMarried'],
        ]));

        $persons = [$this->createPerson(), $this->createPerson(), $this->createPerson()];

        $json = $serializer->serialize($persons);

        $this->assertSame(\json_encode([
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

        $this->assertSame(\json_encode([
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
        $this->assertSame('{"foo":"bar","bar":null,"baz":null,"innerDummy":null}', $json);
    }

    /** @test */
    public function it_can_deserialize_abstract_classes()
    {
        $serializer = $this->createSerializer(
            $this->createMapping(
                AbstractVehicle::class,
                [
                    'color' => []
                ],
                [],
                [
                    'discriminatorMap' => [
                        'field' => 'type',
                        'mapping' => [
                            'car' => Car::class,
                            'airplane' => Airplane::class
                        ]
                    ]
                ]
            )
        );

        $serialized = $serializer->serialize(new Car('blue', 2));
        $this->assertSame('{"color":"blue","type":"car"}', $serialized);
    }

    /** @test */
    public function it_can_deserialize_abstract_mixed_with_concrete_classes()
    {
        $serializer = $this->createSerializer(\array_merge(
            $this->createMapping(
                AbstractVehicle::class,
                [
                    'color' => []
                ],
                [],
                [
                    'discriminatorMap' => [
                        'field' => 'type',
                        'mapping' => [
                            'car' => Car::class,
                            'airplane' => Airplane::class
                        ]
                    ]
                ]
            ),
            $this->createMapping(Car::class, [
                'doors' => ['type' => 'integer']
            ])
        ));

        $serialized = $serializer->serialize(new Car("red", 2));
        $this->assertSame('{"color":"red","doors":2,"type":"car"}', $serialized);
    }

    private function createPerson()
    {
        $person = new Person(1, 'Tales', true);
//        $person->setLastName('Santos');

        return $person;
    }
}
