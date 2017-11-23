<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
