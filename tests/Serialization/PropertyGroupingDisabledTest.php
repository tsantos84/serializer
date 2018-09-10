<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

use Tests\TSantos\Serializer\Fixture\Model\Dummy;
use Tests\TSantos\Serializer\SerializerTestCase;
use TSantos\Serializer\SerializationContext;

/**
 * Class PropertyGroupingDisabledTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 *
 * @runTestsInSeparateProcesses
 */
class PropertyGroupingDisabledTest extends SerializerTestCase
{
    /** @test */
    public function it_should_serialize_all_properties()
    {
        $serializer = $this->createSerializer($this->createMapping(Dummy::class, [
            'foo' => ['groups' => ['v1']],
            'bar' => ['groups' => ['v2']],
        ]));

        $person = new Dummy('foo', 'bar');

        $context = SerializationContext::create()->setGroups(['v3']);

        $this->assertSame('{"foo":"foo","bar":"bar"}', $serializer->serialize($person, $context));
    }
}
