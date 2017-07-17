<?php

namespace Tests\TSantos\Serializer;

use Tests\TSantos\Serializer\Fixture\Person;


/**
 * Class CustomGetterTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 *
 * @runTestsInSeparateProcesses
 */
class CustomGetterTest extends SerializerTestCase
{
    public function testSerializeWithCustomGetterProperty()
    {
        $serializer = $this->createSerializer($this->createMapping(Person::class, [
            'birthday' => ['type' => \DateTime::class, 'modifier' => 'format("d/m/Y")']
        ], []));

        $person = (new Person(1, 'Tales', true));
        $person->setBirthday(new \DateTime('1984-11-28'));

        $json = $serializer->serialize($person, 'json');

        $this->assertEquals(json_encode([
            'birthday' => '28/11/1984'
        ]), $json);
    }
}
