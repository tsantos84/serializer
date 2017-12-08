<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\TSantos\Serializer\Serialization;

use Tests\TSantos\Serializer\Fixture\Person;
use Tests\TSantos\Serializer\SerializerTestCase;

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

        $json = $serializer->serialize($person);

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

        $json = $serializer->serialize($persons);

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
