<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Serializer;

use Tests\TSantos\Serializer\Fixture\Vehicle;
use Tests\TSantos\Serializer\SerializerTestCase;

/**
 * Class SerializableObjectsTest
 *
 * @package Tests\Serializer
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 *
 * @runTestsInSeparateProcesses
 */
class SerializationWithDataHandler extends SerializerTestCase
{
    public function testSerializeDateTime()
    {
        $serializer = $this->createSerializer($this->createMapping(Vehicle::class, [
            'color' => [],
            'ports' => ['type'=>'integer']
        ]));

        $person = new Vehicle('white', 4);

        $expected = '{"color":"white","ports":4,"owner":"Tales","tires":{"FL":"good","FR":"medium","BL":"good","BR":"bad"}}';

        $this->assertEquals($expected, $serializer->serialize($person, 'json'));
    }
}
