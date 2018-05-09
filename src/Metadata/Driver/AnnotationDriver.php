<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Serializer\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use Metadata\Driver\DriverInterface;
use TSantos\Serializer\Mapping\BaseClass;
use TSantos\Serializer\Mapping\ExposeAs;
use TSantos\Serializer\Mapping\Getter;
use TSantos\Serializer\Mapping\Groups;
use TSantos\Serializer\Mapping\Modifier;
use TSantos\Serializer\Mapping\ReadOnly;
use TSantos\Serializer\Mapping\Setter;
use TSantos\Serializer\Mapping\Type;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\Metadata\PropertyMetadata;
use TSantos\Serializer\Metadata\VirtualPropertyMetadata;
use TSantos\Serializer\TypeGuesser;

/**
 * Class AnnotationDriver
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class AnnotationDriver implements DriverInterface
{
    /**
     * @var AnnotationReader
     */
    private $reader;

    /**
     * @var TypeGuesser
     */
    private $guesser;

    /**
     * AnnotationDriver constructor.
     * @param AnnotationReader $reader
     * @param TypeGuesser $guesser
     */
    public function __construct(AnnotationReader $reader, TypeGuesser $guesser)
    {
        $this->reader = $reader;
        $this->guesser = $guesser;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $metadata = new ClassMetadata($class->name);

        $this->loadClassAnnotations($class, $metadata);

        $this->loadPropertyAnnotations($class, $metadata);

        $this->loadVirtualPropertyAnnotations($class, $metadata);

        return $metadata;
    }

    /**
     * @param \ReflectionClass $class
     * @param ClassMetadata $metadata
     */
    private function loadClassAnnotations(\ReflectionClass $class, ClassMetadata $metadata): void
    {
        foreach ($this->reader->getClassAnnotations($class) as $annotation) {
            switch (true) {
                case $annotation instanceof BaseClass:
                    $metadata->baseClass = $annotation->name;
                    break;
            }
        }
    }

    /**
     * @param \ReflectionClass $class
     * @param $metadata
     * @throws \ReflectionException
     */
    private function loadPropertyAnnotations(\ReflectionClass $class, ClassMetadata $metadata): void
    {
        foreach ($class->getProperties() as $property) {
            $annotations = $this->filterAnnotations($this->reader->getPropertyAnnotations($property));

            if (empty($annotations)) {
                continue;
            }

            $propertyMetadata = new PropertyMetadata($property->class, $property->name);
            $propertyMetadata->type = $this->guesser->guessProperty($propertyMetadata);
            $getter = 'get' . ucfirst($property->getName());
            $setter = 'set' . ucfirst($property->getName());

            if ($class->hasMethod($getter)) {
                $propertyMetadata->getter = $getter;
                $propertyMetadata->getterRef = new \ReflectionMethod($propertyMetadata->class, $getter);
            }

            if ($class->hasMethod($setter)) {
                $propertyMetadata->setter = $setter;
                $propertyMetadata->setterRef = new \ReflectionMethod($propertyMetadata->class, $setter);
            }

            $this->configureProperty($propertyMetadata, $annotations);

            $metadata->addPropertyMetadata($propertyMetadata);
        }
    }

    /**
     * @param \ReflectionClass $class
     * @param $metadata
     */
    private function loadVirtualPropertyAnnotations(\ReflectionClass $class, ClassMetadata $metadata): void
    {
        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $annotations = $this->filterAnnotations($this->reader->getMethodAnnotations($method));

            if (empty($annotations)) {
                continue;
            }

            $property = new VirtualPropertyMetadata($method->class, $method->name);
            $property->type = $this->guesser->guessVirtualProperty($property);
            $this->configureProperty($property, $annotations);
            $metadata->addMethodMetadata($property);
        }
    }

    /**
     * @param array $annotations
     * @return array
     */
    private function filterAnnotations(array $annotations): array
    {
        $annotations = array_filter($annotations, function ($annotation) {
            $ref = new \ReflectionObject($annotation);
            return strpos($ref->getNamespaceName(), 'TSantos\Serializer') === 0;
        });
        return $annotations;
    }

    private function configureProperty($property, array $annotations)
    {
        $config = [
            Type::class => function($property, Type $annotation) {
                $property->type = $annotation->name;
            },
            ExposeAs::class => function($property, ExposeAs $annotation) {
                $property->exposeAs = $annotation->name;
            },
            Groups::class => function($property, Groups $annotation) {
                $property->groups = (array)$annotation->groups;
            },
            Modifier::class => function($property, Modifier $annotation) {
                $property->modifier = $annotation->name;
            },
            Getter::class => function($property, Getter $annotation) {
                $property->getter = $annotation->name;
                $property->getterRef = new \ReflectionMethod($property->class, $annotation->name);
            },
            Setter::class => function($property, Setter $annotation) {
                $property->setter = $annotation->name;
                $property->setterRef = new \ReflectionMethod($property->class, $annotation->name);
            },
            ReadOnly::class => function($property) {
                $property->readOnly = true;
            },
        ];

        foreach ($annotations as $annotation) {
            $annotationClass = get_class($annotation);
            if (!isset($config[$annotationClass])) {
                continue;
            }

            call_user_func($config[$annotationClass], $property, $annotation);
        }
    }
}
