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
use Tests\TSantos\Serializer\Fixture\Model\Person;
use TSantos\Serializer\Metadata\ClassMetadata;

/**
 * Class AbstractDriverTest.
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
        $this->assertEquals('Tests\TSantos\Serializer\AbstractSerializerClass', $m->baseClass);

        // field 'id'
        $this->assertEquals('integer', $pm['id']->type);
        $this->assertNull($pm['id']->setter);
        $this->assertEquals(['Default'], $pm['id']->groups);
        $this->assertEquals('id', $pm['id']->exposeAs);

        // field 'name'
        $this->assertEquals('string', $pm['name']->type);
        $this->assertEquals(['api'], $pm['name']->groups);

        // field 'lastName'
        $this->assertEquals('string', $pm['lastName']->type);

        // field 'married'
        $this->assertEquals('isMarried', $pm['married']->getter);
        $this->assertEquals('is_married', $pm['married']->exposeAs);

        // field 'father'
        $this->assertEquals(Person::class, $pm['father']->type);

        // field 'birthday'
        $this->assertEquals('DateTime', $pm['birthday']->type);
        $this->assertEquals(['format' => 'd/m/Y'], $pm['birthday']->options);

        // field 'address'
        $this->assertTrue($pm['address']->readOnly);
    }

    /** @test */
    public function it_can_read_virtual_properties_from_metadata()
    {
        $driver = $this->createDriver();
        $m = $driver->loadMetadataForClass(new \ReflectionClass(Person::class));

        // virtual property 'getFullName'
        $this->assertEquals('string', $m->methodMetadata['getFullName']->type);
        $this->assertEquals('full_name', $m->methodMetadata['getFullName']->exposeAs);
        $this->assertEquals(['api'], $m->methodMetadata['getFullName']->groups);

        // virtual property 'getFormattedAddress'
        $this->assertEquals('getFormattedAddress', $m->methodMetadata['getFormattedAddress']->exposeAs);
        $this->assertEquals(['Default'], $m->methodMetadata['getFormattedAddress']->groups);
    }

    abstract public function createDriver(): DriverInterface;
}
