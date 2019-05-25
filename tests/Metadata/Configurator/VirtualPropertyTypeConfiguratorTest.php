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

namespace Tests\TSantos\Serializer\Metadata\Configurator;

use TSantos\Serializer\Metadata\Configurator\VirtualPropertyTypeConfigurator;
use TSantos\Serializer\Metadata\VirtualPropertyMetadata;

/**
 * Class VirtualPropertyTypeConfiguratorTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class VirtualPropertyTypeConfiguratorTest extends AbstractConfiguratorTest
{
    protected function setUp(): void
    {
        $this->configurator = new VirtualPropertyTypeConfigurator();
    }

    /** @test */
    public function it_should_not_configure_type_if_it_is_already_defined()
    {
        $subject = new class() {
            public function getBirthday(): string
            {
            }
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new VirtualPropertyMetadata($classMetadata->name, 'getBirthday');
        $property->type = 'some_type';
        $classMetadata->addMethodMetadata($property);
        $this->configurator->configure($classMetadata);
        $this->assertSame('some_type', $property->type);
    }

    /** @test */
    public function it_should_defaults_type_to_string()
    {
        $subject = new class() {
            public function getFullName()
            {
            }
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new VirtualPropertyMetadata($classMetadata->name, 'getFullName');
        $classMetadata->addMethodMetadata($property);
        $this->configurator->configure($classMetadata);
        $this->assertSame('string', $property->type);
    }

    /** @test */
    public function it_should_guess_type_from_the_built_in_return_type()
    {
        $subject = new class() {
            public function getAge(): int
            {
            }
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new VirtualPropertyMetadata($classMetadata->name, 'getAge');
        $classMetadata->addMethodMetadata($property);
        $this->configurator->configure($classMetadata);
        $this->assertSame('integer', $property->type);
    }

    /** @test */
    public function it_should_guess_type_from_the_dock_block()
    {
        $subject = new class() {
            /** @return int */
            public function getAge()
            {
            }
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new VirtualPropertyMetadata($classMetadata->name, 'getAge');
        $classMetadata->addMethodMetadata($property);
        $this->configurator->configure($classMetadata);
        $this->assertSame('integer', $property->type);
    }
}
