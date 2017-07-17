<?php

namespace Tests\TSantos\Serializer;

use Tests\TSantos\Serializer\Fixture\Address;
use Tests\TSantos\Serializer\Fixture\Coordinates;
use Tests\TSantos\Serializer\Fixture\Person;
use Tests\TSantos\Serializer\Fixture\Vehicle;
use TSantos\Serializer\SerializationContext;

/**
 * Class MaxDepthSerializationTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 *
 * @runTestsInSeparateProcesses
 */
class MaxDepthSerializationTest extends SerializerTestCase
{
    public function testSerializeWithMaxDepth()
    {
        $personMapping = $this->createMapping(Person::class, [
            'id' => [],
            'name' => [],
            'address' => ['type' => Address::class],
            'married' => ['getter' => 'isMarried']
        ]);

        $addressMapping = $this->createMapping(Address::class, [
            'city' => [],
            'coordinates' => ['type' => Coordinates::class]
        ]);

        $coordinateMapping = $this->createMapping(Coordinates::class, [
            'x' => ['type' => 'float'],
            'y' => ['type' => 'float'],
        ]);

        $mappings = array_merge($personMapping, $addressMapping, $coordinateMapping);

        $serializer = $this->createSerializer($mappings);

        $person = new Person(1, 'Tales', true);
        $person->setAddress($address = new Address());
        $address->setCity('Belo Horizonte');
        $address->setCoordinates(new Coordinates(10, 20));

        $json = $serializer->serialize($person, 'json', SerializationContext::create()->setMaxDepth(2));

        $this->assertEquals(json_encode([
            'id' => 1,
            'name' => 'Tales',
            'address' => [
                'city' => 'Belo Horizonte',
                'coordinates' => []
            ],
            'married' => true
        ]), $json);
    }

    public function testSerializeWithMaxDepthOnPlainArray()
    {
        $serializer = $this->createSerializer();

        $data = [
            1,
            2,
            3,
            "four",
            "five" => [
                'six'
            ],
            "seven" => [
                "eight" => [
                    "nine"
                ]
            ]
        ];

        $json = $serializer->serialize($data, 'json', SerializationContext::create()->setMaxDepth(2));
        $this->assertEquals('{"0":1,"1":2,"2":3,"3":"four","five":["six"],"seven":{"eight":[]}}', $json);
    }

    public function testSerializeWithMaxDepthOnJsonSerializableInterface()
    {
        $serializer = $this->createSerializer($this->createMapping(Vehicle::class, [
            'color' => [],
            'ports' => ['type'=>'integer']
        ]));

        $person = new Vehicle('white', 4);

        $expected = '{"color":"white","ports":4,"owner":"Tales","tires":[]}';

        $this->assertEquals($expected, $serializer->serialize($person, 'json', SerializationContext::create()->setMaxDepth(1)));
    }
}
