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

namespace Tests\TSantos\Serializer\Metadata\Driver;

use PHPUnit\Framework\TestCase;
use Tests\TSantos\Serializer\Fixture\Model\Person;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\Metadata\Driver\CallbackDriver;
use TSantos\Serializer\Metadata\PropertyMetadata;

/**
 * Class CallbackDriverTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class CallbackDriverTest extends TestCase
{
    /** @test */
    public function it_should_call_the_callback_provided()
    {
        $callback = function (\ReflectionClass $class) {
            $metadata = new ClassMetadata($class->name);
            $metadata->addPropertyMetadata(new PropertyMetadata(Person::class, 'id'));

            return $metadata;
        };

        $driver = new CallbackDriver($callback);

        $metadata = $driver->loadMetadataForClass(new \ReflectionClass(Person::class));

        $this->assertEquals(Person::class, $metadata->name);
        $this->assertCount(1, $metadata->propertyMetadata);
        $this->assertArrayHasKey('id', $metadata->propertyMetadata);
    }

    /**
     * @test
     * @expectedException \BadMethodCallException
     */
    public function it_should_not_allow_invalid_metadata_callback_return()
    {
        $callback = function (\ReflectionClass $class) {
            $metadata = new ClassMetadata($class->name);
            $metadata->addPropertyMetadata(new PropertyMetadata(Person::class, 'id'));
        };

        $driver = new CallbackDriver($callback);

        $driver->loadMetadataForClass(new \ReflectionClass(Person::class));
    }
}
