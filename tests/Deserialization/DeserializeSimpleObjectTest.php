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

use Tests\TSantos\Serializer\Fixture\Book;
use Tests\TSantos\Serializer\Fixture\Person;
use Tests\TSantos\Serializer\SerializerTestCase;
use TSantos\Serializer\DeserializationContext;

/**
 * Class DeserializeObjectTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 * @runTestsInSeparateProcesses
 */
class DeserializeSimpleObjectTest extends SerializerTestCase
{
    /** @test */
    public function it_can_deserialize_a_simple_object()
    {
        $serializer = $this->createSerializer(array_merge(
            $this->createMapping(Person::class, [
                'name' => ['type' => 'string'],
                'favouriteBook' => ['type' => Book::class]
            ]),
            $this->createMapping(Book::class, [
                'id' => ['type' => 'integer'],
                'name' => ['type' => 'string']
            ])
        ));

        $content = <<<EOF
{
    "name":"Tales Santos",
    "favouriteBook": {
        "id":10,
        "name":"Design Patterns"
    }
}
EOF;

        /** @var Person $person */
        $person = $serializer->deserialize($content, Person::class);

        $this->assertEquals('Tales Santos', $person->getName());
        $this->assertInstanceOf(Book::class, $person->getFavouriteBook());
        $this->assertEquals(10, $person->getFavouriteBook()->getId());
        $this->assertEquals('Design Patterns', $person->getFavouriteBook()->getName());
    }

    /** @test */
    public function it_cannot_deserialize_read_only_attributes()
    {
        $serializer = $this->createSerializer(array_merge(
            $this->createMapping(Person::class, [
                'id' => ['type' => 'integer', 'readOnly' => true],
                'name' => ['type' => 'string'],
            ])
        ));

        $content = <<<EOF
{
    "id":10,
    "name":"Tales Augusto Santos"
}
EOF;
        $person = new Person(10, 'Tales Santos');
        $context = new DeserializationContext();
        $context->setTarget($person);

        /** @var Person $person */
        $person = $serializer->deserialize($content, Person::class, $context);

        $this->assertEquals('Tales Augusto Santos', $person->getName());
        $this->assertEquals(10, $person->getId());
    }
}
