<?php

declare(strict_types=1);

/*
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
use TSantos\Serializer\SerializationContext;

/**
 * Class PropertyGroupsTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 *
 * @runTestsInSeparateProcesses
 */
class PropertyGroupsTest extends SerializerTestCase
{
    /** @test */
    public function it_should_serialize_only_properties_specified_in_group_list()
    {
        $serializer = $this->createSerializer($this->createMapping(Person::class, [
            'id' => ['type' => 'integer', 'groups' => ['web']],
            'name' => ['type' => 'string', 'groups' => ['mobile']],
            'married' => ['type' => 'boolean', 'getter' => 'isMarried'],
        ]));

        $person = new Person(1, 'Tales', true);

        $expected = \json_encode(['id' => 1, 'married' => true]);

        $this->assertSame($expected, $serializer->serialize($person, SerializationContext::create()->setGroups(['web', 'Default'])));
    }
}
