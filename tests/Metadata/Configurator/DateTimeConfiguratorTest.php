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
 * Class DateTimeConfiguratorTest.
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
        $subject = new class() {
            private $publishedAt;
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new PropertyMetadata($classMetadata->name, 'publishedAt');
        $property->type = \DateTime::class;
        $classMetadata->addPropertyMetadata($property);

        $configurator = new DateTimeConfigurator();
        $configurator->configure($classMetadata);

        $this->assertEquals(sprintf('$value->format(\'%s\')', \DateTime::ISO8601), $property->readValueFilter);
        $this->assertEquals(sprintf('\DateTime::createFromFormat(\'%s\', $value)', \DateTime::ISO8601), $property->writeValueFilter);
    }

    /** @test */
    public function it_should_configure_the_accessors_with_date_format_defined_on_metadata()
    {
        $subject = new class() {
            private $publishedAt;
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new PropertyMetadata($classMetadata->name, 'publishedAt');
        $property->type = \DateTime::class;
        $property->options['format'] = 'd/m/Y';
        $classMetadata->addPropertyMetadata($property);

        $this->configurator->configure($classMetadata);

        $this->assertEquals('$value->format(\'d/m/Y\')', $property->readValueFilter);
        $this->assertEquals('\DateTime::createFromFormat(\'d/m/Y\', $value)', $property->writeValueFilter);
    }

    /** @test */
    public function it_should_not_change_the_accessors_if_they_already_are_defined()
    {
        $subject = new class() {
            private $publishedAt;
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new PropertyMetadata($classMetadata->name, 'publishedAt');
        $property->type = \DateTime::class;
        $property->readValueFilter = 'some_read';
        $property->writeValueFilter = 'some_write';
        $classMetadata->addPropertyMetadata($property);

        $this->configurator->configure($classMetadata);

        $this->assertEquals('some_read', $property->readValueFilter);
        $this->assertEquals('some_write', $property->writeValueFilter);
    }

    /** @test */
    public function it_configure_only_properties_typed_with_datetime()
    {
        $subject = new class() {
            private $id;
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new PropertyMetadata($classMetadata->name, 'id');
        $property->type = 'integer';
        $classMetadata->addPropertyMetadata($property);

        $this->configurator->configure($classMetadata);

        $this->assertNull($property->readValueFilter);
        $this->assertNull($property->writeValueFilter);
    }
}
