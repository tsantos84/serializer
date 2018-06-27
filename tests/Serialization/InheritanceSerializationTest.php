<?php

declare(strict_types=1);
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Serializer;

use Tests\TSantos\Serializer\Fixture\Model\Person;
use Tests\TSantos\Serializer\Fixture\Model\Employee;
use Tests\TSantos\Serializer\SerializerTestCase;

/**
 * Class InheritanceSerializationTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 * @runTestsInSeparateProcesses
 */
class InheritanceSerializationTest extends SerializerTestCase
{
    /** @test */
    public function it_can_serialize_an_employee_which_inherits_from_person()
    {
        $employee = new Employee(1, 'Tales', true);
        $employee->setPosition('Developer');

        $serializer = $this->createSerializer(\array_merge(
            $this->createMapping(Person::class, [
                'name' => [],
            ]),
            $this->createMapping(Employee::class, [
                'position' => [],
            ])
        ));

        $expected = '{"name":"Tales","position":"Developer"}';

        $this->assertEquals($expected, $serializer->serialize($employee));
    }

    /** @test */
    public function it_can_deserialize_an_employee_which_inherits_from_person()
    {
        $serializer = $this->createSerializer(\array_merge(
            $this->createMapping(Person::class, [
                'name' => [],
            ]),
            $this->createMapping(Employee::class, [
                'position' => [],
            ])
        ));

        $content = '{"name":"Tales","position":"Developer"}';

        $employee = $serializer->deserialize($content, Employee::class);

        $this->assertEquals('Tales', $employee->getName());
        $this->assertEquals('Developer', $employee->getPosition());
    }
}
