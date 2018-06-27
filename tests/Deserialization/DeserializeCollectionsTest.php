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

use Tests\TSantos\Serializer\Fixture\Model\Book;
use Tests\TSantos\Serializer\Fixture\Model\Person;
use Tests\TSantos\Serializer\SerializerTestCase;

/**
 * Class DeserializeArrayOfObjectTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 * @runTestsInSeparateProcesses
 */
class DeserializeCollectionsTest extends SerializerTestCase
{
    /** @test */
    public function it_can_deserialize_an_array_of_persons()
    {
        $serializer = $this->createSerializer(array_merge(
            $this->createMapping(Person::class, [
                'name' => ['type' => 'string'],
                'colors' => ['type' => 'array'],
                'favouriteBook' => ['type' => Book::class],
            ]),
            $this->createMapping(Book::class, [
                'id' => ['type' => 'integer'],
                'name' => ['type' => 'string'],
            ])
        ));

        $content = <<<EOF
[
    {
        "name":"Tales Santos",
        "colors":["white","blue"],
        "favouriteBook": {
            "id":10,
            "name":"Design Patterns"
        }
    },
    {
        "name":"Tales Santos",
        "colors":["white","blue"],
        "favouriteBook": {
            "id":10,
            "name":"Design Patterns"
        }
    },
    {
        "name":"Tales Santos",
        "colors":["white","blue"],
        "favouriteBook": {
            "id":10,
            "name":"Design Patterns"
        }
    }
]
EOF;

        /** @var Person[] $persons */
        $persons = $serializer->deserialize($content, sprintf('%s[]', Person::class));

        $this->assertCount(3, $persons);

        foreach ($persons as $person) {
            $this->assertEquals('Tales Santos', $person->getName());
            $this->assertEquals(['white', 'blue'], $person->getColors());
            $this->assertInstanceOf(Book::class, $person->getFavouriteBook());
            $this->assertEquals(10, $person->getFavouriteBook()->getId());
            $this->assertEquals('Design Patterns', $person->getFavouriteBook()->getName());
        }
    }

    /** @test */
    public function it_can_deserialize_an_array_of_integers()
    {
        $serializer = $this->createSerializer();
        $content = '[1,2,3,4,5,6,7,8,9,10]';
        $collection = $serializer->deserialize($content, 'integer[]');
        $this->assertSame([1, 2, 3, 4, 5, 6, 7, 8, 9, 10], $collection);
    }

    /** @test */
    public function it_can_deserialize_an_untyped_array_and_keep_its_items_data_type()
    {
        $serializer = $this->createSerializer();
        $content = '[1,2,"3",4,5,6,7,8,9,10]';
        $collection = $serializer->deserialize($content, 'array');
        $this->assertSame([1, 2, '3', 4, 5, 6, 7, 8, 9, 10], $collection);
    }

    /** @test */
    public function it_can_deserialize_an_array_of_strings()
    {
        $serializer = $this->createSerializer();
        $content = '[1,2,3,4,5,6,7,8,9,10]';
        $collection = $serializer->deserialize($content, 'string[]');
        $this->assertSame(['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'], $collection);
    }

    /** @test */
    public function it_can_deserialize_an_array_of_float()
    {
        $serializer = $this->createSerializer();
        $content = '[1.1,2.2,3.3,4.4,5.5,6.6,7.7,8.8,9.9,10.11]';
        $collection = $serializer->deserialize($content, 'float[]');
        $this->assertSame([1.1, 2.2, 3.3, 4.4, 5.5, 6.6, 7.7, 8.8, 9.9, 10.11], $collection);
    }
}
