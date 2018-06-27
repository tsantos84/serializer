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

namespace Tests\TSantos\Serializer\Serialization;

use Tests\TSantos\Serializer\SerializerTestCase;

/**
 * Class ScalarTypesSerializationTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 *
 * @runTestsInSeparateProcesses
 */
class ScalarTypesSerializationTest extends SerializerTestCase
{
    public function testSerializeWithScalarValue()
    {
        $serializer = $this->createSerializer([]);
        $json = $serializer->serialize(1);
        $this->assertEquals(1, $json);
    }

    public function testSerializeWithSimpleArray()
    {
        $serializer = $this->createSerializer([]);
        $json = $serializer->serialize([1, 2, 3, 'four']);
        $this->assertEquals(\json_encode([1, 2, 3, 'four']), $json);
    }
}
