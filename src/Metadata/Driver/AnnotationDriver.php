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
use Metadata\MergeableClassMetadata;
use TSantos\Serializer\Mapping\ExposeAs;
use TSantos\Serializer\Mapping\Getter;
use TSantos\Serializer\Mapping\Groups;
use TSantos\Serializer\Mapping\Modifier;
use TSantos\Serializer\Mapping\Type;
use TSantos\Serializer\Metadata\PropertyMetadata;
use TSantos\Serializer\Metadata\VirtualPropertyMetadata;

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
     * AnnotationDriver constructor.
     * @param AnnotationReader $reader
     */
    public function __construct(AnnotationReader $reader)
    {
        $this->reader = $reader;
    }

    public function getAllClassNames()
    {
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $metadata = new MergeableClassMetadata($className = $class->name);

        foreach ($class->getProperties() as $property) {
            if (count($annotations = $this->reader->getPropertyAnnotations($property))) {
                $propertyMetadata = new PropertyMetadata($property->class, $property->name);
                foreach ($annotations as $annotation) {
                    $this->configureProperty($propertyMetadata, $annotation);
                }
                $metadata->addPropertyMetadata($propertyMetadata);
            }
        }

        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $virtualPropertyMetadata = new VirtualPropertyMetadata($method->class, $method->name);
            if (count($annotations = $this->reader->getMethodAnnotations($method))) {
                foreach ($annotations as $annotation) {
                    $this->configureVirtualProperty($virtualPropertyMetadata, $annotation);
                }
                $metadata->addMethodMetadata($virtualPropertyMetadata);
            }
        }

        return $metadata;
    }

    private function configureProperty(PropertyMetadata $metadata, $annotation)
    {
        switch (true) {
            case $annotation instanceof Type:
                $metadata->type = $annotation->name;
                break;
            case $annotation instanceof Getter:
                $metadata->accessor = $annotation->name . '()';
                $metadata->getterRef = new \ReflectionMethod($metadata->class, $annotation->name);
                break;
            case $annotation instanceof ExposeAs:
                $metadata->exposeAs = $annotation->name;
                break;
            case $annotation instanceof Groups:
                $metadata->groups = $annotation->groups;
                break;
            case $annotation instanceof Modifier:
                $metadata->modifier = $annotation->name;
                break;
        }

        return $metadata;
    }

    private function configureVirtualProperty(VirtualPropertyMetadata $metadata, $annotation)
    {
        switch (true) {
            case $annotation instanceof Type:
                $metadata->type = $annotation->name;
                break;
            case $annotation instanceof ExposeAs:
                $metadata->exposeAs = $annotation->name;
                break;
            case $annotation instanceof Groups:
                $metadata->groups = $annotation->groups;
                break;
            case $annotation instanceof Modifier:
                $metadata->modifier = $annotation->name;
                break;
        }

        return $metadata;
    }
}
