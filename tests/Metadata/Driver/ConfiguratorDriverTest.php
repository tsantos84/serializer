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
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\Metadata\ConfiguratorInterface;
use TSantos\Serializer\Metadata\Driver\ConfiguratorDriver;

/**
 * Class ConfiguratorDriverTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class ConfiguratorDriverTest extends TestCase
{
    /** @test */
    public function it_should_configure_the_metadata()
    {
        $reflection = $this->createMock(\ReflectionClass::class);
        $metadata = $this->createMock(ClassMetadata::class);

        $driver = $this->createMock(DriverInterface::class);
        $driver
            ->expects($this->once())
            ->method('loadMetadataForClass')
            ->with($reflection)
            ->willReturn($metadata);

        $configurator = $this->createMock(ConfiguratorInterface::class);
        $configurator
            ->expects($this->once())
            ->method('configure')
            ->with($metadata);

        $configurators = [$configurator];

        $configuratorDriver = new ConfiguratorDriver($driver, $configurators);
        $configuratorDriver->loadMetadataForClass($reflection);
    }

    /** @test */
    public function it_should_not_pass_null_driver_to_configurators_chain()
    {
        $reflection = $this->createMock(\ReflectionClass::class);
        $metadata = $this->createMock(ClassMetadata::class);

        $driver = $this->createMock(DriverInterface::class);
        $driver
            ->expects($this->once())
            ->method('loadMetadataForClass')
            ->with($reflection)
            ->willReturn(null);

        $configurator = $this->createMock(ConfiguratorInterface::class);
        $configurator
            ->expects($this->never())
            ->method('configure')
            ->with($metadata);

        $configurators = [$configurator];

        $configuratorDriver = new ConfiguratorDriver($driver, $configurators);
        $configuratorDriver->loadMetadataForClass($reflection);
    }
}
