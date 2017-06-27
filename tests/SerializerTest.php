<?php

namespace Tests\TSantos\Serializer;

use PHPUnit\Framework\TestCase;
use Tests\TSantos\Serializer\Fixture\Address;
use Tests\TSantos\Serializer\Fixture\Coordinates;
use TSantos\Serializer\SerializationContext;
use TSantos\Serializer\SerializerBuilder;
use TSantos\Serializer\Metadata\Driver\ArrayDriver;
use Tests\TSantos\Serializer\Fixture\Person;
use TSantos\Serializer\SerializerInterface;

/** @runTestsInSeparateProcesses */
class SerializerTest extends TestCase
{
    private $cacheDir;

    protected function setUp()
    {
        $this->cacheDir = sys_get_temp_dir() . '/serializer/cache';
    }

    protected function tearDown()
    {
        system('rm -rf ' . escapeshellarg($this->cacheDir), $retval);
    }

    public function testSerializeSimpleObject()
    {
        $serializer = $this->createSerializer($this->createMapping(Person::class, [
            'id' => [],
            'name' => [],
            'married' => ['getter' => 'isMarried']
        ]));

        $person = $this->createPerson();

        $json = $serializer->serialize($person, 'json');

        $this->assertEquals(json_encode([
            'id' => 1,
            'name' => 'Tales',
            'married' => true
        ]), $json);
    }

    public function testSerializeWithVirtualProperty()
    {
        $serializer = $this->createSerializer($this->createMapping(Person::class, [], [
            'fullName' => []
        ]));

        $person = $this->createPerson();

        $json = $serializer->serialize($person, 'json');

        $this->assertEquals(json_encode([
            'fullName' => 'Tales Santos'
        ]), $json);
    }

    public function testSerializeWithScalarValue()
    {
        $serializer = $this->createSerializer([]);
        $json = $serializer->serialize(1, 'json');
        $this->assertEquals(json_encode([1]), $json);
    }

    public function testSerializeWithSimpleArray()
    {
        $serializer = $this->createSerializer([]);
        $json = $serializer->serialize([1, 2, 3, "four"], 'json');
        $this->assertEquals(json_encode([1, 2, 3, "four"]), $json);
    }

    public function testSerializeWithCollectionOfPerson()
    {
        $serializer = $this->createSerializer($this->createMapping(Person::class, [
            'id' => [],
            'name' => [],
            'married' => ['getter' => 'isMarried']
        ]));

        $persons = [$this->createPerson(), $this->createPerson(), $this->createPerson()];

        $json = $serializer->serialize($persons, 'json');

        $this->assertEquals(json_encode([
            [
                'id' => 1,
                'name' => 'Tales',
                'married' => true
            ],
            [
                'id' => 1,
                'name' => 'Tales',
                'married' => true
            ],
            [
                'id' => 1,
                'name' => 'Tales',
                'married' => true
            ]
        ]), $json);
    }

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

        $person = $this->createPerson();
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

    private function createPerson()
    {
        $person = new Person();
        $person->setId(1);
        $person->setName('Tales');
        $person->setLastName('Santos');
        $person->setMarried(true);

        return $person;
    }

    private function createSerializer(array $mapping = []): SerializerInterface
    {
        $builder = new SerializerBuilder();

        $builder
            ->setMetadataDriver(new ArrayDriver($mapping))
            ->setCacheDir($this->cacheDir)
            ->setDebug(true);

        return $builder->build();
    }

    private function createMapping(string $type, array $properties, array $virtualProperties = []): array
    {
        return [
            $type => [
                'properties' => $properties,
                'virtual_properties' => $virtualProperties
            ]
        ];
    }
}
