<?php

namespace Tests\Serializer;

use Tests\TSantos\Serializer\Fixture\Person;
use Tests\TSantos\Serializer\Fixture\Employee;
use Tests\TSantos\Serializer\SerializerTestCase;

/**
 * Class ByPassingSerializationTest
 *
 * @package Tests\Serializer
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 * @runTestsInSeparateProcesses
 */
class InheritanceSerializationTest extends SerializerTestCase
{
    public function testSingleInheritanceObjects()
    {
        $employee = new Employee(1, 'Tales', true);
        $employee->setPosition('Developer');

        $serializer = $this->createSerializer(array_merge(
            $this->createMapping(Person::class, [
                'name' => []
            ]),
            $this->createMapping(Employee::class, [
                'position' => []
            ])
        ));

        $expected = '{"name":"Tales","position":"Developer"}';

        $this->assertEquals($expected, $serializer->serialize($employee, 'json'));
    }
}
