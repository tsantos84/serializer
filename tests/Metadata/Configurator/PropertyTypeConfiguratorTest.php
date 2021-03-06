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

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Tests\TSantos\Serializer\Fixture\Model\Person;
use TSantos\Serializer\Metadata\Configurator\PropertyTypeConfigurator;
use TSantos\Serializer\Metadata\PropertyMetadata;

/**
 * Class PropertyTypeConfiguratorTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class PropertyTypeConfiguratorTest extends AbstractConfiguratorTest
{
    protected function setUp(): void
    {
        $propertyInfo = new PropertyInfoExtractor([], [
            new ReflectionExtractor(),
            new PhpDocExtractor(),
        ]);

        $this->configurator = new PropertyTypeConfigurator($propertyInfo);
    }

    /** @test */
    public function it_should_not_configure_property_type_if_it_is_already_defined()
    {
        $subject = new class() {
            private $id;
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new PropertyMetadata($classMetadata->name, 'id');
        $property->type = 'some_type';
        $classMetadata->addPropertyMetadata($property);
        $this->configurator->configure($classMetadata);
        $this->assertSame('some_type', $property->type);
    }

    /** @test */
    public function it_should_guess_type_from_the_built_in_return_type()
    {
        $subject = new class() {
            private $id;

            public function getId(): int
            {
                return $this->id;
            }
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new PropertyMetadata($classMetadata->name, 'id');
        $classMetadata->addPropertyMetadata($property);
        $this->configurator->configure($classMetadata);
        $this->assertSame('integer', $property->type);
    }

    /** @test */
    public function it_should_guess_type_from_the_doc_block()
    {
        $subject = new class() {
            private $published;

            /**
             * @return bool
             */
            public function isPublished()
            {
                return $this->published;
            }
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new PropertyMetadata($classMetadata->name, 'published');
        $classMetadata->addPropertyMetadata($property);
        $this->configurator->configure($classMetadata);
        $this->assertSame('boolean', $property->type);
    }

    /** @test */
    public function it_should_guess_type_from_the_property_doc_block()
    {
        $subject = new class() {
            /** @var bool */
            private $published;
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new PropertyMetadata($classMetadata->name, 'published');
        $classMetadata->addPropertyMetadata($property);
        $this->configurator->configure($classMetadata);
        $this->assertSame('boolean', $property->type);
    }

    /** @test */
    public function it_should_defaults_the_type_to_string_if_there_is_no_getter_nor_property_docblock()
    {
        $subject = new class() {
            private $name;
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new PropertyMetadata($classMetadata->name, 'name');
        $classMetadata->addPropertyMetadata($property);
        $this->configurator->configure($classMetadata);
        $this->assertSame('string', $property->type);
    }

    /** @test */
    public function it_should_defaults_the_type_to_string_if_there_is_getter_but_without_any_return_type()
    {
        $subject = new class() {
            private $name;

            public function getName()
            {
                return $this->name;
            }
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new PropertyMetadata($classMetadata->name, 'name');
        $classMetadata->addPropertyMetadata($property);
        $this->configurator->configure($classMetadata);
        $this->assertSame('string', $property->type);
    }

    /** @test */
    public function it_should_defaults_the_type_to_string_if_the_docblock_returns_mixed_type()
    {
        $subject = new class() {
            private $name;

            /**
             * @return mixed
             */
            public function getName()
            {
                return $this->name;
            }
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new PropertyMetadata($classMetadata->name, 'name');
        $classMetadata->addPropertyMetadata($property);
        $this->configurator->configure($classMetadata);
        $this->assertSame('string', $property->type);
    }

    /** @test */
    public function it_should_guess_type_from_its_default_value()
    {
        $subject = new class() {
            private $age = 30;
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new PropertyMetadata($classMetadata->name, 'age');
        $classMetadata->addPropertyMetadata($property);
        $this->configurator->configure($classMetadata);
        $this->assertSame('integer', $property->type);
    }

    /** @test */
    public function it_should_guess_type_from_its_setter_method_type_hint()
    {
        $subject = new class() {
            private $age;

            public function setAge(int $age)
            {
            }
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new PropertyMetadata($classMetadata->name, 'age');
        $classMetadata->addPropertyMetadata($property);
        $this->configurator->configure($classMetadata);
        $this->assertSame('integer', $property->type);
    }

    /** @test */
    public function it_should_guess_type_from_its_setter_method_doc_block_param_annotation()
    {
        $subject = new class() {
            private $age;

            /**
             * @param int $age
             */
            public function setAge($age)
            {
            }
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new PropertyMetadata($classMetadata->name, 'age');
        $classMetadata->addPropertyMetadata($property);
        $this->configurator->configure($classMetadata);
        $this->assertSame('integer', $property->type);
    }

    /** @test */
    public function it_should_guess_type_from_constructor_param_type_hint()
    {
        $subject = new class(33) {
            private $age;

            public function __construct(int $age)
            {
            }
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new PropertyMetadata($classMetadata->name, 'age');
        $classMetadata->addPropertyMetadata($property);
        $this->configurator->configure($classMetadata);
        $this->assertSame('integer', $property->type);
    }

    /** @test */
    public function it_should_extract_type_for_a_collection_of_builtin_type()
    {
        $subject = new class() {
            private $comments;

            public function addComment(string $comment)
            {
            }
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new PropertyMetadata($classMetadata->name, 'comments');
        $classMetadata->addPropertyMetadata($property);
        $this->configurator->configure($classMetadata);
        $this->assertSame('string[]', $property->type);
    }

    /** @test */
    public function it_should_extract_type_for_a_collection_of_non_built_in_type()
    {
        $subject = new class() {
            private $persons;

            public function addPerson(Person $person)
            {
            }
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new PropertyMetadata($classMetadata->name, 'persons');
        $classMetadata->addPropertyMetadata($property);
        $this->configurator->configure($classMetadata);
        $this->assertSame(Person::class.'[]', $property->type);
    }

    /** @test */
    public function it_should_extract_type_for_a_collection_of_unknown_type()
    {
        $subject = new class() {
            /**
             * @var array
             */
            private $roles;
        };

        $classMetadata = $this->createClassMetadata($subject);
        $property = new PropertyMetadata($classMetadata->name, 'roles');
        $classMetadata->addPropertyMetadata($property);
        $this->configurator->configure($classMetadata);
        $this->assertSame('mixed[]', $property->type);
    }
}
