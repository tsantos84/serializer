<?php

namespace Serializer;

use Metadata\ClassMetadata;

/**
 * Class AbstractSerializerClass
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
abstract class AbstractSerializerClass implements SerializerClassInterface
{
    protected $classMetadata;
    protected $serializer;

    public function __construct(Serializer $serializer, ClassMetadata $metadata)
    {
        $this->serializer = $serializer;
        $this->classMetadata = $metadata;
    }

    /**
     * @param string $property
     * @param SerializationContext $context
     * @return bool
     */
    protected function isPropertyGroupExposed(string $property, SerializationContext $context)
    {
        $propertyGroups = $this->classMetadata->propertyMetadata[$property]->groups;
        $contextGroups = $context->getGroups();
        return count(array_intersect($propertyGroups, $contextGroups)) > 0;
    }

    /**
     * @param string $property
     * @param SerializationContext $context
     * @return bool
     */
    protected function isVirtualPropertyGroupExposed(string $property, SerializationContext $context)
    {
        $propertyGroups = $this->classMetadata->methodMetadata[$property]->groups;
        $contextGroups = $context->getGroups();
        return count(array_intersect($propertyGroups, $contextGroups)) > 0;
    }
}
