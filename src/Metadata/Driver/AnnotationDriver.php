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
        $metadata = new ClassMetadata($className = $class->name);

        foreach ($this->reader->getClassAnnotations($class) as $annotation) {
            switch (true) {
                case $annotation instanceof BaseClass:
                    $metadata->baseClass = $annotation->name;
                    break;
            }
        }

        foreach ($class->getProperties() as $property) {
            $annotations = array_filter($this->reader->getPropertyAnnotations($property), function ($annotation) {
                $ref = new \ReflectionObject($annotation);
                return strpos($ref->getNamespaceName(), 'TSantos\Serializer') === 0;
            });
            if (!empty($annotations)) {
                $propertyMetadata = new PropertyMetadata($property->class, $property->name);
                $hasTypeAnnotation = false;
                $hasGetterAnnotation = false;
                $hasSetterAnnotation = false;
                foreach ($annotations as $annotation) {
                    switch (true) {
                        case $annotation instanceof Type:
                            $propertyMetadata->type = $annotation->name;
                            $hasTypeAnnotation = true;
                            break;
                        case $annotation instanceof Getter:
                            $propertyMetadata->getter = $annotation->name;
                            $propertyMetadata->getterRef =
                                new \ReflectionMethod($propertyMetadata->class, $annotation->name);
                            $hasGetterAnnotation = true;
                            break;
                        case $annotation instanceof Setter:
                            $propertyMetadata->setter = $annotation->name;
                            $propertyMetadata->setterRef =
                                new \ReflectionMethod($propertyMetadata->class, $annotation->name);
                            $hasSetterAnnotation = true;
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
                if (!$hasTypeAnnotation) {
                    $propertyMetadata->type = $this->guesser->guessProperty($propertyMetadata);
                }
                if (!$hasGetterAnnotation && $class->hasMethod($getter = 'get' . ucfirst($property->getName()))) {
                    $propertyMetadata->getter = $getter;
                    $propertyMetadata->getterRef = new \ReflectionMethod($propertyMetadata->class, $getter);
                }
                if (!$hasSetterAnnotation && $class->hasMethod($setter = 'set' . ucfirst($property->getName()))) {
                    $propertyMetadata->setter = $setter;
                    $propertyMetadata->setterRef = new \ReflectionMethod($propertyMetadata->class, $setter);
                }
                $metadata->addPropertyMetadata($propertyMetadata);
            }
        }

        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $virtualPropertyMetadata = new VirtualPropertyMetadata($method->class, $method->name);
            $annotations = array_filter($this->reader->getMethodAnnotations($method), function ($annotation) {
                $ref = new \ReflectionObject($annotation);
                return strpos($ref->getNamespaceName(), 'TSantos\Serializer') === 0;
            });
            if (count($annotations)) {
                $hasTypeAnnotation = false;
                $hasExposeAsAnnotation = false;
                foreach ($annotations as $annotation) {
                    switch (true) {
                        case $annotation instanceof Type:
                            $virtualPropertyMetadata->type = $annotation->name;
                            $hasTypeAnnotation = true;
                            break;
                        case $annotation instanceof ExposeAs:
                            $virtualPropertyMetadata->exposeAs = $annotation->name;
                            $hasExposeAsAnnotation = true;
                            break;
                        case $annotation instanceof Groups:
                            $virtualPropertyMetadata->groups = $annotation->groups;
                            break;
                        case $annotation instanceof Modifier:
                            $virtualPropertyMetadata->modifier = $annotation->name;
                            break;
                    }
                }
                if (!$hasTypeAnnotation) {
                    $virtualPropertyMetadata->type = $this->guesser->guessVirtualProperty($virtualPropertyMetadata);
                }
                if (!$hasExposeAsAnnotation) {
                    $virtualPropertyMetadata->exposeAs = $virtualPropertyMetadata->name;
                }
                $metadata->addMethodMetadata($virtualPropertyMetadata);
            }
        }

        return $metadata;
    }
}
