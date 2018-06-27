<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\TSantos\Serializer\Metadata\Configurator;

use TSantos\Serializer\Metadata\Configurator\SetterConfigurator;
use TSantos\Serializer\Metadata\PropertyMetadata;

class SetterConfiguratorTest extends AbstractConfiguratorTest
{
    protected function setUp()
    {
        $this->configurator = new SetterConfigurator();
    }

    /** @test */
    public function it_should_not_change_the_setter_if_it_already_is_defined()
    {
        $subject = new class() {
            public $name;
        };

        $classMetadata = $this->createClassMetadata($subject);
        $classMetadata->addPropertyMetadata($property = new PropertyMetadata($classMetadata->name, 'name'));
        $property->setter = 'someSetter';
        $this->configurator->configure($classMetadata);
        $this->assertEquals('someSetter', $property->setter);
    }

    /** @test */
    public function it_should_configure_the_setter_directly_on_property()
    {
        $subject = new class() {
            public $name;
        };

        $classMetadata = $this->createClassMetadata($subject);
        $classMetadata->addPropertyMetadata($property = new PropertyMetadata($classMetadata->name, 'name'));
        $this->configurator->configure($classMetadata);
        $this->assertEquals('name', $property->setter);
    }

    /** @test */
    public function it_should_configure_property_to_write_from_setter_method()
    {
        $subject = new class() {
            private $name;

            public function setName()
            {
            }
        };

        $classMetadata = $this->createClassMetadata($subject);
        $classMetadata->addPropertyMetadata($property = new PropertyMetadata($classMetadata->name, 'name'));
        $this->configurator->configure($classMetadata);
        $this->assertEquals('setName', $property->setter);
    }
}
