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

namespace TSantos\Serializer\CodeDecorator;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use TSantos\Serializer\CodeDecoratorInterface;
use TSantos\Serializer\DeserializationContext;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\Metadata\PropertyMetadata;

/**
 * Class HydrationDecorator.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class HydrationDecorator implements CodeDecoratorInterface
{
    public function decorate(PhpFile $file, PhpNamespace $namespace, ClassType $class, ClassMetadata $classMetadata): void
    {
        $hydrate = $class->addMethod('hydrate')
            ->setVisibility('public')
            ->addComment('@param ' . $classMetadata->name . ' $object')
            ->addComment('@param array $data')
            ->addComment('@param ' . DeserializationContext::class . ' $context')
            ->addComment('@return mixed')
        ;

        $hydrate
            ->addParameter('object');

        $hydrate
            ->addParameter('data')
            ->setTypeHint('array');

        $hydrate
            ->addParameter('context')
            ->setTypeHint(DeserializationContext::class);

        $hydrate->setBody($this->createHydrateMethodBody($classMetadata));
    }

    private function createHydrateMethodBody(ClassMetadata $classMetadata): string
    {
        if (!$classMetadata->hasProperties()) {
            return 'return $object;';
        }

        $body = <<<STRING
if (null !== \$context->getGroups()) {

    \$contextId = \$context->getId();

    if (!isset(self::\$exposedPropertiesForContext[\$contextId])) {
        self::\$exposedPropertiesForContext[\$contextId] = \$this->getExposedKeys(\$context);
    }
    
    \$data = \array_intersect_key(\$data, self::\$exposedPropertiesForContext[\$contextId]);
}
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
if (isset(\$data['{exposeAs}']) || \array_key_exists('{exposeAs}', \$data)) {
    if (null !== \$value = \$data['{exposeAs}']) {
        {mutator}
    } else {
        \$object->{setter}(null);
    }
}

STRING;

        if ($property->writeValueFilter) {
            $mutator = \sprintf('$object->%s(%s);', $property->setter, $property->writeValueFilter);
        } elseif ($property->isScalarType()) {
            $mutator = \sprintf('$object->%s((%s) $value);', $property->setter, $property->type);
        } elseif ($property->isCollection()) {
            $template = <<<STRING
foreach (\$value as \$key => \$val) {
            \$value[\$key] = {reader};
        }
        \$object->{setter}(\$value);
STRING;

            if ($property->isScalarCollectionType()) {
                $reader = \sprintf('(%s) $val', $property->getTypeOfCollection());
            } elseif ($property->isMixedCollectionType()) {
                $reader = '$val';
            } else {
                $reader = \sprintf('$this->serializer->denormalize($val, \'%s\' $context);', $property->type);
            }

            $mutator = \strtr($template, [
                '{reader}' => $reader,
                '{setter}' => $property->setter,
                '{exposeAs}' => $property->exposeAs,
            ]);
        } else {
            $mutator = \sprintf('$object->%s($this->serializer->denormalize($value, \'%s\', $context));', $property->setter, $property->type);
        }

        return \strtr($body, [
            '{exposeAs}' => $property->exposeAs,
            '{propertyName}' => $property->name,
            '{setter}' => $property->setter,
            '{type}' => $property->type,
            '{mutator}' => $mutator,
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
}
