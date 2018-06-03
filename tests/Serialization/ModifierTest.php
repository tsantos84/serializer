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
 * Class CustomGetterTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 *
 * @runTestsInSeparateProcesses
 */
class ModifierTest extends SerializerTestCase
{
    /** @test */
    public function it_can_read_data_with_custom_modifier()
    {
        $serializer = $this->createSerializer($this->createMapping(Person::class, [
            'birthday' => [
                'type' => \DateTime::class,
                'readValue' => '$value->format("d/m/Y")',
                'writeValue' => '\DateTime::createFromFormat("d/m/Y", $value)'
            ]
        ]));

        $person = new Person(1, 'Tales', true);
        $person->setBirthday(new \DateTime('1984-11-28'));

        $this->assertEquals('{"birthday":"28\/11\/1984"}', $serializer->serialize($person));
    }

    /** @test */
    public function it_can_write_data_with_custom_modifier()
    {
        $serializer = $this->createSerializer($this->createMapping(Person::class, [
            'birthday' => [
                'type' => \DateTime::class,
                'readValue' => '$value->format("d/m/Y")',
                'writeValue' => '\DateTime::createFromFormat("d/m/Y", $value)'
            ]
        ]));

        $json = '{"birthday":"28\/11\/1984"}';
        $person = $serializer->deserialize($json, Person::class);
        $this->assertInstanceOf(\DateTime::class, $person->getBirthday());
    }
}
