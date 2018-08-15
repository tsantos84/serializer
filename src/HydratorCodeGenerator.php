<?php

declare(strict_types=1);
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Serializer;

use Nette\PhpGenerator\Helpers;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\Metadata\PropertyMetadata;
use TSantos\Serializer\Metadata\VirtualPropertyMetadata;
use TSantos\Serializer\Traits\SerializerAwareTrait;

/**
 * Class HydratorCodeGenerator.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class HydratorCodeGenerator
{
    /**
     * @param ClassMetadata $classMetadata
     *
     * @return string
     */
    public function generate(ClassMetadata $classMetadata): string
    {
        $groups = $this->getGroups($classMetadata);

        $hierarchy = [];

        $ref = $classMetadata->reflection;
        do {
            $hierarchy[] = $ref->getName();
        } while ($ref = $ref->getParentClass());

        $phpFile = new PhpFile();

        $namespace = $phpFile->addNamespace('');

        $class = $namespace
            ->addClass($this->getClassName($classMetadata))
            ->setComment('THIS CLASS WAS GENERATED BY THE SERIALIZER. DO NOT EDIT THIS FILE.')
            ->setFinal(true)
            ->setImplements([HydratorInterface::class, SerializerAwareInterface::class]);

        if (null !== $classMetadata->baseClass) {
            $class->addExtend($classMetadata->baseClass);
        }

        $class
            ->addProperty('exposedGroups')
            ->setVisibility('private')
            ->setStatic(true)
            ->setValue($groups);

        $class
            ->addProperty('exposedPropertiesForContext')
            ->setVisibility('private')
            ->setStatic(true)
            ->setValue([]);

        $class->addTrait(SerializerAwareTrait::class);

        $extract = $this->createExtractMethodSignature();
        $extract->setBody($this->createExtractMethodBody($classMetadata));

        $hydrate = $this->createHydrateMethodSignature();
        $hydrate->setBody($this->createHydrateMethodBody($classMetadata));

        $exposedKeys = $this->createExposedKeysMethod();
        $reflectionProperty = $this->createReflectionPropertyMethod($hierarchy);

        $class->setMethods([$extract, $hydrate, $exposedKeys, $reflectionProperty]);

        return Helpers::tabsToSpaces((string) $phpFile, 4);
    }

    public function getClassName(ClassMetadata $classMetadata): string
    {
        return \str_replace('\\', '', $classMetadata->name).'Hydrator';
    }

    private function createExtractMethodSignature(): Method
    {
        $extract = (new Method('extract'))
            ->setReturnType('array')
            ->setVisibility('public');

        $extract
            ->addParameter('object');

        $extract
            ->addParameter('context')
            ->setTypeHint(SerializationContext::class);

        return $extract;
    }

    private function createExtractMethodBody(ClassMetadata $classMetadata): string
    {
        $body = <<<STRING
if (!\$object instanceof {$classMetadata->name}) {
    throw new \InvalidArgumentException(sprintf('%s is able to serialize only instances of "%s" only. "%s" given', get_class(\$this), '{$classMetadata->name}', is_object(\$object) ? get_class(\$object) : gettype(\$object)));
}

STRING;

        if (!$classMetadata->hasProperties()) {
            $body .= 'return [];';

            return $body;
        }

        $body .= <<<STRING
\$data = [];
\$shouldSerializeNull = \$context->shouldSerializeNull();
{accessors}
\$contextId = \$context->getId();

if (!isset(self::\$exposedPropertiesForContext[\$contextId])) {
    self::\$exposedPropertiesForContext[\$contextId] = \$this->getExposedKeys(\$context);
}

\$data = array_intersect_key(\$data, self::\$exposedPropertiesForContext[\$contextId]);

return \$data;
STRING;
        $accessors = '';

        /** @var PropertyMetadata $property */
        foreach ($classMetadata->propertyMetadata as $property) {
            if ($property->getter) {
                $accessors .= $this->createAccessorCode($property, '$object->'.$property->getter.'()');
                continue;
            }

            $propCode = <<<STRING
\$propReflection = \$this->getReflectionProperty('{declaringClass}', '{propertyName}');
\$propReflection->setAccessible(true);

STRING;
            $propCode .= $this->createAccessorCode($property, '$propReflection->getValue($object)');

            $accessors .= \strtr($propCode, [
                '{declaringClass}' => $property->reflection->getDeclaringClass()->name,
                '{propertyName}' => $property->name,
            ]);
        }

        /** @var VirtualPropertyMetadata $property */
        foreach ($classMetadata->methodMetadata as $property) {
            $accessors .= $this->createAccessorCode($property, '$object->'.$property->name.'()');
        }

        return \strtr($body, ['{accessors}' => $accessors]);
    }

    /**
     * @param PropertyMetadata|VirtualPropertyMetadata $property
     * @param string                                   $accessor
     *
     * @return string
     */
    private function createAccessorCode($property, string $accessor): string
    {
        $code = <<<STRING

// property {propertyName}
if (null !== \$value = {accessor}) {
    {formatter} 
} elseif (\$shouldSerializeNull) {
    \$data['{exposeAs}'] = null;
}

STRING;

        if ($property->readValueFilter) {
            $formatter = sprintf('$data[\'%s\'] = %s', $property->exposeAs, $property->readValueFilter);
        } elseif ($property->isScalarType()) {
            $formatter = sprintf('$data[\'%s\'] = (%s) $value;', $property->exposeAs, $property->type);
        } elseif ($property->isScalarCollectionType()) {
            $formatter = <<<STRING

    \$context->enter();
    if (\$context->isMaxDeepAchieve()) {
        \$data['%s'] = [];
    } else {
        foreach (\$value as \$key => \$val) {
            \$value[\$key] = (%s) \$val;
        }
        \$data['%s'] = \$value;
    }
    \$context->leave();
STRING;
            $formatter = sprintf($formatter, $property->exposeAs, $property->getTypeOfCollection(), $property->exposeAs);

        } else {
            $formatter = sprintf('$data[\'%s\'] = $this->serializer->normalize($value, $context);', $property->exposeAs);
        }

        $replaces = [
            '{exposeAs}' => $property->exposeAs,
            '{propertyName}' => $property->name,
            '{formatter}' => $formatter,
            '{accessor}' => $accessor
        ];

        return \strtr($code, $replaces);
    }

    private function createHydrateMethodSignature(): Method
    {
        $hydrate = (new Method('hydrate'))
            ->setVisibility('public');

        $hydrate
            ->addParameter('object');

        $hydrate
            ->addParameter('data')
            ->setTypeHint('array');

        $hydrate
            ->addParameter('context')
            ->setTypeHint(DeserializationContext::class);

        return $hydrate;
    }

    private function createHydrateMethodBody(ClassMetadata $classMetadata): string
    {
        $body = <<<STRING
if (!\$object instanceof {$classMetadata->name}) {
    throw new \InvalidArgumentException(sprintf('%s is able to deserialize only instances of "%s" only. "%s" given', get_class(\$this), '{$classMetadata->name}', is_object(\$object) ? get_class(\$object) : gettype(\$object)));
}

STRING;

        if (!$classMetadata->hasProperties()) {
            return $body.'return $object;';
        }

        $body .= <<<STRING
static \$contextKeys = [];
\$contextId = \$context->getId();

if (!isset(\$contextKeys[\$contextId])) {
    \$contextKeys[\$contextId] = \$this->getExposedKeys(\$context);
}

\$data = array_intersect_key(\$data, \$contextKeys[\$contextId]);
{mutatorBody}
return \$object;
STRING;

        $mutatorBody = '';

        /** @var PropertyMetadata[] $properties */
        $properties = $classMetadata->getWritableProperties();

        foreach ($properties as $property) {
            if ($property->setter) {
                $mutatorBody .= $this->createHydrateMutatorBody($property);
                continue;
            }
            $mutatorBody .= $this->createHydrateReflectionBody($property);
        }

        return \strtr($body, ['{mutatorBody}' => $mutatorBody]);
    }

    private function createHydrateMutatorBody(PropertyMetadata $property): string
    {
        $body = <<<STRING
        
// property {propertyName}
if (isset(\$data['{exposeAs}'])) {
    if (null !== \$value = \$data['{exposeAs}']) {
        \$object->{setter}({value});
    } else {
        \$object->{setter}(null);
    }
}

STRING;

        if ($property->writeValueFilter) {
            $mutator = $property->writeValueFilter;
        } elseif ($property->isScalarType()) {
            $mutator = \sprintf('((%s) $value)', $property->type);
        } else {
            $mutator = \sprintf('$this->serializer->denormalize($value, \'%s\', $context)', $property->type);
        }

        return \strtr($body, [
            '{exposeAs}' => $property->exposeAs,
            '{propertyName}' => $property->name,
            '{setter}' => $property->setter,
            '{type}' => $property->type,
            '{value}' => $mutator,
        ]);
    }

    private function createHydrateReflectionBody(PropertyMetadata $property): string
    {
        $body = <<<STRING
        
// property {propertyName}
if (isset(\$data['{exposeAs}'])) {
    \$propReflection = \$this->getReflectionProperty('{declaringClass}', '{propertyName}');
    \$propReflection->setAccessible(true);
    if (null !== \$value = \$data['{exposeAs}']) {
        \$propReflection->setValue(\$object, {value});
    } else {
        \$propReflection->setValue(\$object, null);
    }
}

STRING;
        if ($property->writeValueFilter) {
            $value = $property->writeValueFilter;
        } elseif ($property->isScalarType()) {
            $value = '$value';
        } else {
            $value = '$this->serializer->denormalize($value, '.$property->type.', $context)';
        }

        return \strtr($body, [
            '{exposeAs}' => $property->exposeAs,
            '{declaringClass}' => $property->reflection->getDeclaringClass()->name,
            '{propertyName}' => $property->name,
            '{value}' => $value,
        ]);
    }

    private function createExposedKeysMethod(): Method
    {
        $method = (new Method('getExposedKeys'))
            ->setVisibility('private')
            ->setReturnType('array')
            ->setBody(<<<STRING
\$exposedKeys = [];
\$contextGroups = \$context->getGroups();

foreach (\$contextGroups as \$group => \$val) {
    if (isset(static::\$exposedGroups[\$group])) {
        \$exposedKeys = \array_merge(\$exposedKeys, static::\$exposedGroups[\$group]);
    }
}

return \$exposedKeys;
STRING
            );

        $method
            ->addParameter('context')
            ->setTypeHint(AbstractContext::class);

        return $method;
    }

    private function createReflectionPropertyMethod(array $hierarchyClasses): Method
    {
        $classes = \implode(',', \array_map(function (string $class): string {
            return \sprintf("'%s' => new \ReflectionClass('%s')\n", $class, $class);
        }, $hierarchyClasses));

        $method = (new Method('getReflectionProperty'))
            ->setVisibility('private')
            ->setReturnType('\ReflectionProperty')
            ->setBody(<<<STRING
static \$reflections;

if (null === \$reflections) {
    \$reflections = [
        $classes
    ];
}

return \$reflections[\$class]->getProperty(\$property);
STRING
            );

        $method
            ->addParameter('class')
            ->setTypeHint('string');

        $method
            ->addParameter('property')
            ->setTypeHint('string');

        return $method;
    }

    private function getGroups(ClassMetadata $metadata): array
    {
        $groups = [];
        foreach ($metadata->propertyMetadata as $property) {
            foreach ($property->groups as $group) {
                $groups[$group][$property->exposeAs] = true;
            }
        }

        foreach ($metadata->methodMetadata as $method) {
            foreach ($method->groups as $group) {
                $groups[$group][$method->exposeAs] = true;
            }
        }

        return $groups;
    }
}
