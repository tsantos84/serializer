<?php

declare(strict_types=1);

/*
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
use Tests\TSantos\Serializer\Fixture\Model\Inheritance\AbstractVehicle;
use Tests\TSantos\Serializer\Fixture\Model\Inheritance\Airplane;
use Tests\TSantos\Serializer\Fixture\Model\Inheritance\Car;
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
        $this->assertSame('Tests\TSantos\Serializer\AbstractSerializerClass', $m->baseClass);
        $this->assertSame(['foo' => 'bar', 'bar' => '@baz'], $m->hydratorConstructArgs);

        // field 'id'
        $this->assertSame('integer', $pm['id']->type);
        $this->assertNull($pm['id']->setter);
        $this->assertSame(['Default'], $pm['id']->groups);
        $this->assertSame('id', $pm['id']->exposeAs);

        // field 'name'
        $this->assertSame('string', $pm['name']->type);
        $this->assertSame(['api'], $pm['name']->groups);

        // field 'lastName'
        $this->assertSame('string', $pm['lastName']->type);

        // field 'married'
        $this->assertSame('isMarried', $pm['married']->getter);
        $this->assertSame('is_married', $pm['married']->exposeAs);

        // field 'father'
        $this->assertSame(Person::class, $pm['father']->type);

        // field 'birthday'
        $this->assertSame('DateTime', $pm['birthday']->type);
        $this->assertSame(['format' => 'd/m/Y'], $pm['birthday']->options);

        // field 'address'
        $this->assertTrue($pm['address']->readOnly);
    }

    /** @test */
    public function it_can_read_virtual_properties_from_metadata()
    {
        $driver = $this->createDriver();
        $m = $driver->loadMetadataForClass(new \ReflectionClass(Person::class));

        // virtual property 'getFullName'
        $this->assertSame('string', $m->methodMetadata['getFullName']->type);
        $this->assertSame('full_name', $m->methodMetadata['getFullName']->exposeAs);
        $this->assertSame(['api'], $m->methodMetadata['getFullName']->groups);

        // virtual property 'getFormattedAddress'
        $this->assertSame('getFormattedAddress', $m->methodMetadata['getFormattedAddress']->exposeAs);
        $this->assertSame(['Default'], $m->methodMetadata['getFormattedAddress']->groups);
    }

    /** @test */
    public function it_can_read_metadata_information_from_classes_containing_inheritance()
    {
        $driver = $this->createDriver();

        /** @var ClassMetadata $m */
        $m = $driver->loadMetadataForClass(new \ReflectionClass(AbstractVehicle::class));

        $this->assertSame('type', $m->discriminatorField);

        $mapping = [
            'car' => Car::class,
            'airplane' => Airplane::class,
        ];
        $this->assertSame($mapping, $m->discriminatorMapping);
    }

    abstract public function createDriver(): DriverInterface;
}
