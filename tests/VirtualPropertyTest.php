<?php

namespace Tests\TSantos\Serializer;

use Tests\TSantos\Serializer\Fixture\Person;


/**
 * Class VirtualPropertyTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 *
 * @runTestsInSeparateProcesses
 */
class VirtualPropertyTest extends SerializerTestCase
{
    public function testSerializeWithVirtualProperty()
    {
        $serializer = $this->createSerializer($this->createMapping(Person::class, [], [
            'fullName' => []
        ]));

        $person = (new Person(1, 'Tales', true))->setLastName('Santos');

        $json = $serializer->serialize($person, 'json');

        $this->assertEquals(json_encode([
            'fullName' => 'Tales Santos'
        ]), $json);
    }


    public function testSerializeWithVirtualPropertyAndGetterModifier()
    {
        $serializer = $this->createSerializer($this->createMapping(Person::class, [], [
            'birthday' => ['modifier' => 'format("d/m/Y")']
        ]));

        $person = (new Person(1, 'Tales', true));
        $person->setBirthday(new \DateTime('1984-11-28'));

        $json = $serializer->serialize($person, 'json');

        $this->assertEquals(json_encode([
            'birthday' => '28/11/1984'
        ]), $json);
    }
}
