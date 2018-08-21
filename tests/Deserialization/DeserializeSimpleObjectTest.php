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

use Tests\TSantos\Serializer\Fixture\Model\Book;
use Tests\TSantos\Serializer\Fixture\Model\Dummy;
use Tests\TSantos\Serializer\Fixture\Model\Inheritance\AbstractVehicle;
use Tests\TSantos\Serializer\Fixture\Model\Inheritance\Airplane;
use Tests\TSantos\Serializer\Fixture\Model\Inheritance\Car;
use Tests\TSantos\Serializer\Fixture\Model\Person;
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
    /** @test */
    public function it_can_deserialize_a_simple_object()
    {
        $serializer = $this->createSerializer(\array_merge(
            $this->createMapping(Person::class, [
                'name' => ['type' => 'string'],
                'favouriteBook' => ['type' => Book::class],
            ]),
            $this->createMapping(Book::class, [
                'id' => ['type' => 'integer'],
                'name' => ['type' => 'string'],
            ])
        ));

        $content = <<<EOF
{
    "name":"Tales Santos",
    "favouriteBook": {
        "id":10,
        "name":"Design Patterns"
    }
}
EOF;

        /** @var Person $person */
        $person = $serializer->deserialize($content, Person::class);

        $this->assertSame('Tales Santos', $person->getName());
        $this->assertInstanceOf(Book::class, $person->getFavouriteBook());
        $this->assertSame(10, $person->getFavouriteBook()->getId());
        $this->assertSame('Design Patterns', $person->getFavouriteBook()->getName());
    }

    /** @test */
    public function it_can_deserialize_a_simple_object_by_reflection()
    {
        $serializer = $this->createSerializer([
            Dummy::class => new ReflectionDriver(),
        ]);

        $content = '{"foo":"bar"}';

        /** @var Dummy $subject */
        $subject = $serializer->deserialize($content, Dummy::class);

        $this->assertSame('bar', $subject->getFoo());
    }

    /** @test */
    public function it_cannot_deserialize_read_only_attributes()
    {
        $serializer = $this->createSerializer(\array_merge(
            $this->createMapping(Person::class, [
                'id' => ['type' => 'integer', 'readOnly' => true],
                'name' => ['type' => 'string'],
            ])
        ));

        $content = <<<EOF
{
    "id":10,
    "name":"Tales Augusto Santos"
}
EOF;
        $person = new Person(10, 'Tales Santos');
        $context = new DeserializationContext();
        $context->setTarget($person);

        /** @var Person $person */
        $person = $serializer->deserialize($content, Person::class, $context);

        $this->assertSame('Tales Augusto Santos', $person->getName());
        $this->assertSame(10, $person->getId());
    }

    /** @test */
    public function it_can_deserialize_abstract_classes()
    {
        $serializer = $this->createSerializer(\array_merge(
            $this->createMapping(
                AbstractVehicle::class,
                [
                    'color' => [],
                ],
                [],
                [
                    'discriminatorMap' => [
                        'field' => 'type',
                        'mapping' => [
                            'car' => Car::class,
                            'airplane' => Airplane::class,
                        ],
                    ],
                ]
            ),
            $this->createMapping(Car::class, [
                'doors' => ['type' => 'integer'],
            ]),
            $this->createMapping(Airplane::class, [
                'turbines' => ['type' => 'integer'],
            ])
        ));

        $car = $serializer->deserialize('{"color":"blue","type":"car","doors":2}', AbstractVehicle::class);
        $this->assertInstanceOf(Car::class, $car);
        $this->assertSame('blue', $car->getColor());
        $this->assertSame(2, $car->getDoors());

        $airplane = $serializer->deserialize('{"color":"red","type":"airplane","turbines":4}', AbstractVehicle::class);
        $this->assertInstanceOf(Airplane::class, $airplane);
        $this->assertSame('red', $airplane->getColor());
        $this->assertSame(4, $airplane->getTurbines());
    }
}
