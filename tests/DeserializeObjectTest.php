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

use Tests\TSantos\Serializer\Fixture\Book;
use Tests\TSantos\Serializer\Fixture\Person;

/**
 * Class DeserializeObjectTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 * @runTestsInSeparateProcesses
 */
class DeserializeObjectTest extends SerializerTestCase
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
        $person = $serializer->deserialize($content, Person::class, 'json');

        $this->assertEquals('Tales Santos', $person->getName());
        $this->assertInstanceOf(Book::class, $person->getFavouriteBook());
        $this->assertEquals(10, $person->getFavouriteBook()->getId());
        $this->assertEquals('Design Patterns', $person->getFavouriteBook()->getName());
    }
}
