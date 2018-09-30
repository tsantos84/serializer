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

use TSantos\Serializer\Metadata\Configurator\GetterConfigurator;
use TSantos\Serializer\Metadata\PropertyMetadata;

/**
 * Class GetterConfiguratorTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class GetterConfiguratorTest extends AbstractConfiguratorTest
{
    public function setup()
    {
        $this->configurator = new GetterConfigurator();
    }

    /** @test */
    public function it_should_not_change_the_getter_if_it_is_already_defined()
    {
        $subject = new class() {
            public $id;
        };

        $classMetadata = $this->createClassMetadata($subject);
        $classMetadata->addPropertyMetadata($property = new PropertyMetadata($classMetadata->name, 'id'));
        $property->getter = 'someGetter';
        $this->configurator->configure($classMetadata);
        $this->assertSame('someGetter', $property->getter);
    }

    /** @test */
    public function it_should_configure_property_to_ready_from_getter_method()
    {
        $subject = new class() {
            private $id;

            public function getId()
            {
            }
        };

        $classMetadata = $this->createClassMetadata($subject);
        $classMetadata->addPropertyMetadata($property = new PropertyMetadata($classMetadata->name, 'id'));
        $this->configurator->configure($classMetadata);
        $this->assertSame('getId', $property->getter);
    }

    /** @test */
    public function it_should_configure_property_to_ready_from_isser_method()
    {
        $subject = new class() {
            private $published;

            public function isPublished()
            {
            }
        };

        $classMetadata = $this->createClassMetadata($subject);
        $classMetadata->addPropertyMetadata($property = new PropertyMetadata($classMetadata->name, 'published'));
        $this->configurator->configure($classMetadata);
        $this->assertSame('isPublished', $property->getter);
    }

    /** @test */
    public function it_should_configure_property_to_ready_from_hasser_method()
    {
        $subject = new class() {
            private $address;

            public function hasAddress()
            {
            }
        };

        $classMetadata = $this->createClassMetadata($subject);
        $classMetadata->addPropertyMetadata($property = new PropertyMetadata($classMetadata->name, 'address'));
        $this->configurator->configure($classMetadata);
        $this->assertSame('hasAddress', $property->getter);
    }
}
