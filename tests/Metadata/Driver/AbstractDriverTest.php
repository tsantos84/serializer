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

use Metadata\Driver\DriverInterface;
use PHPUnit\Framework\TestCase;
use Tests\TSantos\Serializer\Fixture\Person;
use TSantos\Serializer\Metadata\ClassMetadata;

/**
 * Class AbstractDriverTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
abstract class AbstractDriverTest extends TestCase
{
    /** @test */
    public function it_can_read_properties_from_metadata()
    {
        $driver = $this->createDriver();

        /** @var ClassMetadata $m */
        $m = $driver->loadMetadataForClass(new \ReflectionClass(Person::class));
        $pm = $m->propertyMetadata;

        $this->assertInstanceOf(ClassMetadata::class, $m);
        $this->assertEquals('integer', $pm['id']->type);
        $this->assertEquals('string', $pm['name']->type);
        $this->assertEquals(Person::class, $pm['father']->type);
        $this->assertEquals("format('d/m/Y')", $pm['birthday']->modifier);
        $this->assertEquals('Tests\TSantos\Serializer\AbstractSerializerClass', $m->baseClass);
        $this->assertTrue($pm['address']->readOnly);
    }

    /** @test */
    public function it_can_read_virtual_properties_from_metadata()
    {
        $driver = $this->createDriver();
        $m = $driver->loadMetadataForClass(new \ReflectionClass(Person::class));

        $this->assertArrayHasKey('getFullName', $m->methodMetadata);
        $this->assertEquals('full_name', $m->methodMetadata['getFullName']->exposeAs);
    }

    public abstract function createDriver(): DriverInterface;
}
