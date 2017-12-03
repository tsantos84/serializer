<?php

namespace Tests\TSantos\Serializer;

use TSantos\Serializer\Metadata\Driver\ReflectionDriver;
use PHPUnit\Framework\TestCase;
use TSantos\Serializer\TypeGuesser;

/**
 * Class ReflectionDriverTest
 *
 * @package Tests\Serializer\Metadata\Driver
 * @author Tales Santos <tales.maxmilhas@gmail.com>
 */
class ReflectionDriverTest extends TestCase
{
    public function test_read_metadata_from_class_reflection()
    {
        $object = new class {
            /** @var integer */
            public $foo;
            public $bar;
            private $baz;

            /** @return float */
            public function getBar(): float { return $this->bar; }

            /** @return array */
            public function getBaz(): array { return $this->baz; }
        };

        $class = get_class($object);

        $driver = new ReflectionDriver(new TypeGuesser());

        $metadata = $driver->loadMetadataForClass(new \ReflectionClass($class));

        $foo = $metadata->propertyMetadata['foo'];
        $this->assertEquals('integer', $foo->type);
        $this->assertEquals('foo', $foo->accessor);

        $bar = $metadata->propertyMetadata['bar'];
        $this->assertEquals('float', $bar->type);
        $this->assertEquals('getBar()', $bar->accessor);

        $baz = $metadata->propertyMetadata['baz'];
        $this->assertEquals('array', $baz->type);
        $this->assertEquals('getBaz()', $baz->accessor);
    }
}
