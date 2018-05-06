<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Serializer;

use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\Metadata\PropertyMetadata;

/**
 * Class SerializerClassCodeGenerator
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class SerializerClassCodeGenerator
{
    /**
     * @param ClassMetadata $classMetadata
     * @return string
     */
    public function generate(ClassMetadata $classMetadata): string
    {
        return $this->classDeclaration($classMetadata, $this->getClassName($classMetadata));
    }

    public function getClassName(ClassMetadata $classMetadata)
    {
        return str_replace('\\', '', $classMetadata->name) . 'Serializer';
    }

    private function classDeclaration(ClassMetadata $metadata, string $className): string
    {
        return <<<EOF
<?php

use TSantos\Serializer\Exception\InvalidArgumentException;
use TSantos\Serializer\AbstractContext;
use TSantos\Serializer\SerializationContext;
use TSantos\Serializer\DeserializationContext;
use {$metadata->name};

/**
 * THIS CLASS WAS GENERATED BY THE SERIALIZER. DO NOT EDIT THIS FILE.
 * @internal
 */
final class $className extends $metadata->baseClass
{
    private \$exposedGroups = {$this->renderExposedGroups($metadata)};

{$this->serializeMethod($metadata)}

{$this->deserializeMethod($metadata)}

{$this->getExposedKeysMethod()}
}

EOF;
    }

    private function serializeMethod(ClassMetadata $metadata): string
    {
        return <<<EOF
    /**
     * @param {$this->getSimpleClassName($metadata)} \$object
     * @param SerializationContext \$context
     * @return array
     */
    public function serialize(\$object, SerializationContext \$context): array
    {
{$this->serializeMethodBody($metadata)}
    }
EOF;
    }

    private function serializeMethodBody(ClassMetadata $metadata): string
    {
        $code = $this->renderInvalidTypeException($metadata, 'serialize');

        $code .= <<<EOF
        
        \$data = [];
        \$exposedKeys = \$this->getExposedKeys(\$context);
        \$shouldSerializeNull = \$context->shouldSerializeNull();

EOF;
        $code .=
            $this->propertySerializationCode($metadata) .
            $this->virtualPropertySerializationCode($metadata);

        $code .= <<<EOF
        return \$data;
EOF;

        return $code;
    }

    private function propertySerializationCode(ClassMetadata $metadata): string
    {
        $code = '';

        foreach ($metadata->propertyMetadata as $property) {
            if (null === $property->getter) {
                continue;
            }
            $getter = "\$object->{$property->getter}()";
            $value = '$value';
            if (null !== $property->modifier) {
                $value .= '->' . $property->modifier;
            }

            $code .= <<<EOF
        #property '$property->name'
        if (isset(\$exposedKeys['$property->name'])) {
            if (null !== \$value = $getter) {
                {$this->renderSerializationValue($property, $value)}
            } elseif (\$shouldSerializeNull) {
                \$data['$property->exposeAs'] = null;
            }
        }

EOF;
        }

        return $code;
    }

    private function virtualPropertySerializationCode(ClassMetadata $metadata): string
    {
        $code = '';

        foreach ($metadata->methodMetadata as $property) {
            $getter = "\$object->{$property->name}()";
            $value = '$value';
            if (null !== $property->modifier) {
                $value .= '->' . $property->modifier;
            }

            $code .= <<<EOF
        #virtual property '$property->name'
        if (isset(\$exposedKeys['$property->name'])) {
            if (null !== \$value = $getter) {
                {$this->renderSerializationValue($property, $value)}
            } elseif (\$shouldSerializeNull) {
                \$data['$property->exposeAs'] = null;
            }
        }

EOF;

        }

        return $code;
    }

    private function deserializeMethod(ClassMetadata $metadata): string
    {
        $simpleClassName = $this->getSimpleClassName($metadata);
        return <<<EOF
    /**
     * @param $simpleClassName \$object
     * @param array \$data
     * @param DeserializationContext \$context
     * @return $simpleClassName
     */
    public function deserialize(\$object, array \$data, DeserializationContext \$context)
    {
{$this->deserializeMethodBody($metadata)}
    }
EOF;
    }

    private function deserializeMethodBody(ClassMetadata $metadata): string
    {
        $code = $this->renderInvalidTypeException($metadata, 'deserialize');

        $code .= <<<EOF

        \$exposedKeys = \$this->getExposedKeys(\$context);

EOF;

        $code .= $this->propertyDeserializationCode($metadata);

        $code .= <<<EOF

        return \$object;
EOF;

        return $code;
    }

    private function propertyDeserializationCode(ClassMetadata $metadata): string
    {
        $code = '';

        foreach ($metadata->propertyMetadata as $property) {
            if ($property->readOnly || null === $property->setter) {
                continue;
            }
            $code .= <<<EOF
        #property '$property->name'
        if (isset(\$data['$property->exposeAs']) && isset(\$exposedKeys['$property->name'])) {
            if (null !== \$value = \$data['$property->exposeAs']) {
                {$this->renderSetter($property)}
            } else {
                \$object->$property->setter(null);
            }
        }

EOF;
        }

        return $code;
    }

    private function renderSerializationValue($property, string $value)
    {
        if ($this->isScalarType($property->type)) {
            return <<<EOF
\$data['$property->exposeAs'] = {$this->castType($value, $property->type)};
EOF;
        } else {
            return <<<EOF
\$data['$property->exposeAs'] = \$this->serializer->normalize($value, \$context);
EOF;
        }
    }

    private function renderSetter(PropertyMetadata $property)
    {
        if ($this->isScalarType($property->type)) {
            return <<<EOF
\$object->$property->setter({$this->castType("\$data['$property->exposeAs']", $property->type)});
EOF;
        } else {
            return <<<EOF
\$object->$property->setter(\$this->serializer->denormalize(\$data['$property->exposeAs'], '$property->type', \$context));
EOF;

        }
    }

    private function renderInvalidTypeException(ClassMetadata $metadata, string $direction)
    {
        $simpleClassName = $this->getSimpleClassName($metadata);

        return <<<EOF
        if (!\$object instanceof $simpleClassName) {
            throw new InvalidArgumentException(sprintf('%s is able to $direction only instances of "%s" only. "%s" given', get_class(\$this), $simpleClassName::class, is_object(\$object) ? get_class(\$object) : gettype(\$object)));
        }

EOF;
    }

    private function castType(string $value, string $type)
    {
        return sprintf('(%s) %s', $type, $value);
    }

    private function isScalarType(string $type)
    {
        return in_array($type, ['integer', 'string', 'float', 'boolean']);
    }

    private function getSimpleClassName(ClassMetadata $metadata)
    {
        $parts = explode('\\', $metadata->reflection->getName());
        return end($parts);
    }

    private function renderExposedGroups(ClassMetadata $metadata)
    {
        $groups = [];
        foreach ($metadata->propertyMetadata as $property) {
            foreach ($property->groups as $group) {
                $groups[$group][] = $property->name;
            }
        }

        foreach ($metadata->methodMetadata as $method) {
            foreach ($method->groups as $group) {
                $groups[$group][] = $method->name;
            }
        }

        return var_export($groups, true);
    }

    private function getExposedKeysMethod()
    {
        return <<<EOF
    /**
     * @param AbstractContext \$context
     * @return array
     */
    final private function getExposedKeys(AbstractContext \$context)
    {
        if (\$this->computedGroupKeys->contains(\$context)) {
            return \$this->computedGroupKeys[\$context];
        }

        \$contextGroups = array_flip(\$context->getGroups());

        \$computedKeys = array_flip(array_reduce(array_intersect_key(\$this->exposedGroups, \$contextGroups), function (\$g, \$v) {
            array_push(\$g, ...\$v);
            return \$g;
        }, []));

        \$this->computedGroupKeys->attach(\$context, \$computedKeys);

        return \$computedKeys;
    }
EOF;

    }
}
