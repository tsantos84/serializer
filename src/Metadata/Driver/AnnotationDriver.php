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

            foreach ($annotations as $annotation) {
                switch (true) {
                    case $annotation instanceof Type:
                        $propertyMetadata->type = $annotation->name;
                        break;
                    case $annotation instanceof Getter:
                        $getter = $annotation->name;
                        break;
                    case $annotation instanceof Setter:
                        $setter = $annotation->name;
                        break;
                    case $annotation instanceof Groups:
                        $propertyMetadata->groups = $annotation->groups;
                        break;
                    case $annotation instanceof ExposeAs:
                        $propertyMetadata->exposeAs = $annotation->name;
                        break;
                    case $annotation instanceof Modifier:
                        $propertyMetadata->modifier = $annotation->name;
                        break;
                    case $annotation instanceof ReadOnly:
                        $propertyMetadata->readOnly = true;
                        break;
                }
            }

            if ($class->hasMethod($getter)) {
                $propertyMetadata->getter = $getter;
                $propertyMetadata->getterRef = new \ReflectionMethod($propertyMetadata->class, $getter);
            }

            if ($class->hasMethod($setter)) {
                $propertyMetadata->setter = $setter;
                $propertyMetadata->setterRef = new \ReflectionMethod($propertyMetadata->class, $setter);
            }

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
            $property = new VirtualPropertyMetadata($method->class, $method->name);

            $annotations = $this->filterAnnotations($this->reader->getMethodAnnotations($method));

            if (empty($annotations)) {
                continue;
            }

            $property->type = $this->guesser->guessVirtualProperty($property);

            foreach ($annotations as $annotation) {
                switch (true) {
                    case $annotation instanceof Type:
                        $property->type = $annotation->name;
                        break;
                    case $annotation instanceof ExposeAs:
                        $property->exposeAs = $annotation->name;
                        break;
                    case $annotation instanceof Groups:
                        $property->groups = $annotation->groups;
                        break;
                    case $annotation instanceof Modifier:
                        $property->modifier = $annotation->name;
                        break;
                }
            }
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
}
