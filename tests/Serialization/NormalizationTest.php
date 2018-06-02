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

use Tests\TSantos\Serializer\Fixture\Model\Book;
use Tests\TSantos\Serializer\Fixture\Model\Person;
use Tests\TSantos\Serializer\SerializerTestCase;
use TSantos\Serializer\Normalizer\DateTimeNormalizer;

/**
 * Class NormalizationTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 *
 * @runTestsInSeparateProcesses
 */
class NormalizationTest extends SerializerTestCase
{
    public function testSerializeWithDateTimeNormalization()
    {
        $person = new Person(1,'Tales', true);
        $person->setBirthday(new \DateTime('1984-11-28'));

        $serializer = $this->createSerializer($this->createMapping(Person::class, [
            'name' => [],
            'birthday' => ['type' => \DateTimeInterface::class]
        ]));

        $this->assertEquals('{"name":"Tales","birthday":"1984-11-28T00:00:00+00:00"}', $serializer->serialize($person));
    }

    protected function createBuilder()
    {
        $builder = parent::createBuilder();
        $builder->addNormalizer(new DateTimeNormalizer());
        return $builder;
    }
}
