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
use TSantos\Serializer\Metadata\Driver\Annotation\ClassAnnotation;

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

        foreach ($this->reader->getClassAnnotations($class) as $annotation) {
            if ($annotation instanceof ClassAnnotation) {
                $metadata->name = $annotation->name;
            }
        }

        foreach ($class->getProperties() as $property) {
            if ($property->getDeclaringClass()->name === $className) {
                foreach ($this->reader->getPropertyAnnotations($property) as $constraint) {
                    if ($constraint instanceof Constraint) {
                        $metadata->addPropertyConstraint($property->name, $constraint);
                    }
                    $success = true;
                }
            }
        }
    }
}
