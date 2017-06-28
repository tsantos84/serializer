<?php

namespace Tests\TSantos\Serializer;
use Tests\TSantos\Serializer\Fixture\Person;

/**
 * Class CircularReferencePreventionTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 *
 * @runTestsInSeparateProcesses
 */
class CircularReferencePreventionTest extends SerializerTestCase
{
    public function testCircularReferencePrevention()
    {
        $person = new Person(1,'Tales', true);
        $person->setFather($person); // forcing circular reference

        $serializer = $this->createSerializer($this->createMapping(Person::class, [
            'name' => [],
            'father' => ['type' => Person::class]
        ]));

        $this->assertEquals('{"name":"Tales","father":[]}', $serializer->serialize($person, 'json'));
    }
}
