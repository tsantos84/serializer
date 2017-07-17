<?php

namespace Tests\TSantos\Serializer;

use Tests\TSantos\Serializer\Fixture\Book;
use Tests\TSantos\Serializer\Fixture\Person;

/**
 * Class NormalizationTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 *
 * @runTestsInSeparateProcesses
 */
class NormalizationTest extends SerializerTestCase
{
    public function testSerializeWithIdentifiableNormalization()
    {
        $serializer = $this->createSerializer(array_merge(
            $this->createMapping(Person::class, [], [
                'id' => ['type' => 'integer'],
                'name' => ['type' => 'string'],
                'favouriteBook' => ['type' => Book::class]
            ]),
            $this->createMapping(Book::class, [], [
                'id' => ['type' => 'integer'],
                'name' => ['type' => 'string']
            ])
        ));

        $person = new Person(1, 'Tales', true);
        $person->setFavouriteBook(new Book(10, 'Data Transformation'));

        $json = $serializer->serialize($person, 'json');

        $this->assertEquals(json_encode([
            'id' => 1,
            'name' => 'Tales',
            'favouriteBook' => 10
        ]), $json);
    }

    public function testSerializeWithDateTimeNormalization()
    {
        $person = new Person(1,'Tales', true);
        $person->setBirthday(new \DateTime('1984-11-28'));

        $serializer = $this->createSerializer($this->createMapping(Person::class, [
            'name' => [],
            'birthday' => ['type' => \DateTimeInterface::class]
        ]));

        $this->assertEquals('{"name":"Tales","birthday":"1984-11-28T00:00:00+00:00"}', $serializer->serialize($person, 'json'));
    }

    protected function createBuilder()
    {
        $builder = parent::createBuilder();
        $builder->addDefaultNormalizers();
        return $builder;
    }
}
