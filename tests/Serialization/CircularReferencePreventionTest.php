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
 * Class CircularReferencePreventionTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 *
 * @runTestsInSeparateProcesses
 */
class CircularReferencePreventionTest extends SerializerTestCase
{
    /**
     * @test
     * @expectedException \TSantos\Serializer\Exception\CircularReferenceException
     */
    public function it_should_prevent_circular_reference()
    {
        $person = new Person();
        $person->setFather($person); // forcing circular reference

        $serializer = $this->createSerializer($this->createMapping(Person::class, [
            'father' => []
        ]));

        $serializer->serialize($person);
    }

    /**
     * @test
     */
    public function it_should_not_prevent_circular_reference_for_collection_containing_the_same_instance()
    {
        $person = new Person();
        $collection = [];

        for ($i=0; $i < 10; $i++) {
            $collection[] = $person;
        }

        $serializer = $this->createSerializer($this->createMapping(Person::class, [
            'father' => []
        ]));

        $serializer->serialize($collection);

        $this->assertTrue(true);
    }
}
