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

use Tests\TSantos\Serializer\Fixture\Model\Person;
use Tests\TSantos\Serializer\SerializerTestCase;

/**
 * Class VirtualPropertyTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 *
 * @runTestsInSeparateProcesses
 */
class VirtualPropertyTest extends SerializerTestCase
{
    /** @test */
    public function it_can_serialize_a_virtual_property()
    {
        $serializer = $this->createSerializer($this->createMapping(Person::class, [], [
            'getFullName' => ['exposeAs' => 'fullName']
        ]));

        $person = (new Person(1, 'Tales', true))->setLastName('Santos');

        $json = $serializer->serialize($person);

        $this->assertEquals(json_encode([
            'fullName' => 'Tales Santos'
        ]), $json);
    }

    /** @test */
    public function it_can_serialize_a_virtual_property_with_modifier()
    {
        $serializer = $this->createSerializer($this->createMapping(Person::class, [], [
            'getBirthday' => ['exposeAs' => 'birthday', 'modifier' => 'format("d/m/Y")']
        ]));

        $person = (new Person(1, 'Tales', true));
        $person->setBirthday(new \DateTime('1984-11-28'));

        $json = $serializer->serialize($person);

        $this->assertEquals(json_encode([
            'birthday' => '28/11/1984'
        ]), $json);
    }
}
