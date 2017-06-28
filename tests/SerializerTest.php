<?php

namespace Tests\TSantos\Serializer;

use Tests\TSantos\Serializer\Fixture\Person;

/** @runTestsInSeparateProcesses */
class SerializerTest extends SerializerTestCase
{
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
        $person = new Person(1, 'Tales', true);
        $person->setLastName('Santos');

        return $person;
    }
}
