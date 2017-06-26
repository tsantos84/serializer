<?php

namespace Tests\TSantos\Serializer;

use PHPUnit\Framework\TestCase;
use TSantos\Serializer\SerializerBuilder;
use TSantos\Serializer\Metadata\Driver\ArrayDriver;
use TSantos\Serializer\Serializer;
use Tests\TSantos\Serializer\Fixture\Person;

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

    private function createPerson()
    {
        $person = new Person();
        $person->setId(1);
        $person->setName('Tales');
        $person->setLastName('Santos');
        $person->setMarried(true);

        return $person;
    }

    private function createSerializer(array $mapping): Serializer
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
