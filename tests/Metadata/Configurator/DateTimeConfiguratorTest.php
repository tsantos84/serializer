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

use TSantos\Serializer\Metadata\Configurator\DateTimeConfigurator;
use TSantos\Serializer\Metadata\PropertyMetadata;

/**
 * Class DateTimeConfiguratorTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class DateTimeConfiguratorTest extends AbstractConfiguratorTest
{
    protected function setUp()
    {
        $this->configurator = new DateTimeConfigurator();
    }

    /** @test */
    public function it_should_configure_the_accessors_with_default_date_format()
    {
        $subject = new class {
            private $publishedAt;
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new PropertyMetadata($classMetadata->name, 'publishedAt');
        $property->type = \DateTime::class;
        $classMetadata->addPropertyMetadata($property);

        $configurator = new DateTimeConfigurator();
        $configurator->configure($classMetadata);

        $this->assertEquals(sprintf('$value->format(\'%s\')',\DateTime::ISO8601), $property->readValue);
        $this->assertEquals(sprintf('\DateTime::createFromFormat(\'%s\', $value)',\DateTime::ISO8601), $property->writeValue);
    }

    /** @test */
    public function it_should_configure_the_accessors_with_date_format_defined_on_metadata()
    {
        $subject = new class {
            private $publishedAt;
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new PropertyMetadata($classMetadata->name, 'publishedAt');
        $property->type = \DateTime::class;
        $property->options['format'] = 'd/m/Y';
        $classMetadata->addPropertyMetadata($property);

        $this->configurator->configure($classMetadata);

        $this->assertEquals('$value->format(\'d/m/Y\')', $property->readValue);
        $this->assertEquals('\DateTime::createFromFormat(\'d/m/Y\', $value)', $property->writeValue);
    }

    /** @test */
    public function it_should_not_change_the_accessors_if_they_already_are_defined()
    {
        $subject = new class {
            private $publishedAt;
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new PropertyMetadata($classMetadata->name, 'publishedAt');
        $property->type = \DateTime::class;
        $property->readValue = 'some_read';
        $property->writeValue = 'some_write';
        $classMetadata->addPropertyMetadata($property);

        $this->configurator->configure($classMetadata);

        $this->assertEquals('some_read', $property->readValue);
        $this->assertEquals('some_write', $property->writeValue);
    }

    /** @test */
    public function it_configure_only_properties_typed_with_datetime()
    {
        $subject = new class {
            private $id;
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new PropertyMetadata($classMetadata->name, 'id');
        $property->type = 'integer';
        $classMetadata->addPropertyMetadata($property);

        $this->configurator->configure($classMetadata);

        $this->assertNull($property->readValue);
        $this->assertNull($property->writeValue);
    }
}
