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
use Metadata\Driver\AdvancedDriverInterface;
use TSantos\Serializer\Mapping\BaseClass;
use TSantos\Serializer\Mapping\ExposeAs;
use TSantos\Serializer\Mapping\Getter;
use TSantos\Serializer\Mapping\Groups;
use TSantos\Serializer\Mapping\Modifier;
use TSantos\Serializer\Mapping\ReadOnly;
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
class AnnotationDriver implements AdvancedDriverInterface
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

    public function getAllClassNames()
    {
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
            if (count($annotations = $this->reader->getPropertyAnnotations($property))) {
                $propertyMetadata = new PropertyMetadata($property->class, $property->name);
                $hasTypeAnnotation = false;
                $hasGetterAnnotation = false;
                $hasGroupsAnnotation = false;
                $hasExposeAsAnnotation = false;
                foreach ($annotations as $annotation) {
                    switch (true) {
                        case $annotation instanceof Type:
                            $propertyMetadata->type = $annotation->name;
                            $hasTypeAnnotation = true;
                            break;
                        case $annotation instanceof Getter:
                            $propertyMetadata->accessor = $annotation->name . '()';
                            $propertyMetadata->getterRef = new \ReflectionMethod($propertyMetadata->class, $annotation->name);
                            $hasGetterAnnotation = true;
                            break;
                        case $annotation instanceof Groups:
                            $hasGroupsAnnotation = true;
                            break;
                        case $annotation instanceof ExposeAs:
                            $propertyMetadata->exposeAs = $annotation->name;
                            $hasExposeAsAnnotation = true;
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
                if (!$hasGetterAnnotation) {
                    $propertyMetadata->accessor = 'get' . ucfirst($property->getName()) . '()';
                }
                if (!$hasGroupsAnnotation) {
                    $propertyMetadata->groups = ['Default'];
                }
                if (!$hasExposeAsAnnotation) {
                    $propertyMetadata->exposeAs = $propertyMetadata->name;
                }
                $metadata->addPropertyMetadata($propertyMetadata);
            }
        }

        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $virtualPropertyMetadata = new VirtualPropertyMetadata($method->class, $method->name);
            if (count($annotations = $this->reader->getMethodAnnotations($method))) {
                $hasTypeAnnotation = false;
                $hasGroupsAnnotation = false;
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
                            $hasGroupsAnnotation = true;
                            break;
                        case $annotation instanceof Modifier:
                            $virtualPropertyMetadata->modifier = $annotation->name;
                            break;
                    }
                }
                if (!$hasTypeAnnotation) {
                    $virtualPropertyMetadata->type = $this->guesser->guessVirtualProperty($virtualPropertyMetadata);
                }
                if (!$hasGroupsAnnotation) {
                    $virtualPropertyMetadata->groups = ['Default'];
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
