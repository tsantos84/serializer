<?php
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
use TSantos\Serializer\Metadata\Driver\ReflectionDriver;

class ReflectionDriverTest extends TestCase
{
    /** @test */
    public function it_should_add_all_properties_to_class_metadata()
    {
        $subject = new class() {
            private $id;
            private $name;
        };

        $reflection = new \ReflectionClass($subject);

        $driver = new ReflectionDriver();

        $classMetadata = $driver->loadMetadataForClass($reflection);
        $this->assertCount(2, $classMetadata->propertyMetadata);
    }
}
