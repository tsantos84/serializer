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

use Tests\TSantos\Serializer\Fixture\Model\Dummy;
use Tests\TSantos\Serializer\SerializerTestCase;


/**
 * Class SerializeCollectionTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 * @runTestsInSeparateProcesses
 */
class SerializeCollectionTest extends SerializerTestCase
{
    /** @test */
    public function it_can_serialize_a_collection_of_simple_objects()
    {
        $serializer = $this->createSerializer($this->createMapping(Dummy::class, [
            'foo' => ['type' => 'integer'],
        ]));

        $persons = [new Dummy(10), new Dummy(20), new Dummy(30)];

        $this->assertSame('[{"foo":10},{"foo":20},{"foo":30}]', $serializer->serialize($persons));
    }
}
