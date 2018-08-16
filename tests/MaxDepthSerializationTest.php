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

namespace Tests\TSantos\Serializer;

use Tests\TSantos\Serializer\Fixture\Model\Address;
use Tests\TSantos\Serializer\Fixture\Model\Coordinates;
use Tests\TSantos\Serializer\Fixture\Model\Person;
use Tests\TSantos\Serializer\Fixture\Model\Vehicle;
use TSantos\Serializer\SerializationContext;

/**
 * Class MaxDepthSerializationTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 *
 * @runTestsInSeparateProcesses
 */
class MaxDepthSerializationTest extends SerializerTestCase
{
    /**
     * @test
     */
    public function serializeWithMaxDepth()
    {
        $personMapping = $this->createMapping(Person::class, [
            'id' => ['type' => 'integer'],
            'name' => [],
            'address' => ['type' => Address::class],
            'married' => ['type' => 'boolean', 'getter' => 'isMarried'],
        ]);

        $addressMapping = $this->createMapping(Address::class, [
            'city' => [],
            'coordinates' => ['type' => Coordinates::class],
        ]);

        $coordinateMapping = $this->createMapping(Coordinates::class, [
            'x' => ['type' => 'float'],
            'y' => ['type' => 'float'],
        ]);

        $mappings = \array_merge($personMapping, $addressMapping, $coordinateMapping);

        $serializer = $this->createSerializer($mappings);

        $person = new Person(1, 'Tales', true);
        $person->setAddress($address = new Address());
        $address->setCity('Belo Horizonte');
        $address->setCoordinates(new Coordinates(10, 20));

        $json = $serializer->serialize($person, SerializationContext::create()->setMaxDepth(2));

        $this->assertEquals(\json_encode([
            'id' => 1,
            'name' => 'Tales',
            'address' => [
                'city' => 'Belo Horizonte',
                'coordinates' => [],
            ],
            'married' => true,
        ]), $json);
    }

    /**
     * @test
     */
    public function serializeWithMaxDepthOnPlainArray()
    {
        $serializer = $this->createSerializer();

        $data = [
            1,
            2,
            3,
            'four',
            'five' => [
                'six',
            ],
            'seven' => [
                'eight' => [
                    'nine',
                ],
            ],
        ];

        $json = $serializer->serialize($data, SerializationContext::create()->setMaxDepth(2));
        $this->assertEquals('{"0":1,"1":2,"2":3,"3":"four","five":["six"],"seven":{"eight":["nine"]}}', $json);
    }

    /**
     * @test
     */
    public function serializeWithMaxDepthOnJsonSerializableInterface()
    {
        $serializer = $this->createSerializer($this->createMapping(Vehicle::class, [
            'color' => [],
            'ports' => ['type' => 'integer'],
        ]));

        $person = new Vehicle('white', 4);

        $expected = '{"color":"white","ports":4}';

        $this->assertEquals($expected, $serializer->serialize($person, SerializationContext::create()->setMaxDepth(1)));
    }
}
