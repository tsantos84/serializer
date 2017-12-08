<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\TSantos\Serializer\Deserialization;

use Tests\TSantos\Serializer\Fixture\Person;
use Tests\TSantos\Serializer\SerializerTestCase;

/**
 * Class NormalizationTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 *
 * @runTestsInSeparateProcesses
 */
class NormalizationTest extends SerializerTestCase
{
    /** @test */
    public function it_can_deserialize_an_object_containing_an_instance_of_datetime()
    {
        $serializer = $this->createSerializer(array_merge(
            $this->createMapping(Person::class, [
                'id' => ['type' => 'integer'],
                'name' => ['type' => 'string'],
                'birthday' => ['type' => \DateTime::class]
            ])
        ));

        $json = json_encode([
            'id' => 10,
            'name' => 'Tales Santos',
            'birthday' => \DateTime::createFromFormat(\DateTime::ATOM, '1984-11-28T10:00:00+00:00')->format(\DateTime::ATOM)
        ]);
        /** @var Person $person */
        $person = $serializer->deserialize($json, Person::class);

        $this->assertEquals('28/11/1984', $person->getBirthday()->format('d/m/Y'));
    }

    protected function createBuilder()
    {
        $builder = parent::createBuilder();
        $builder->addDefaultNormalizers();
        return $builder;
    }
}
