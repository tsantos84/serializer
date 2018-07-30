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
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * CodeGenerator constructor.
     *
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

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
            ->setValue($groups);

        $class->addTrait(SerializerAwareTrait::class);

        $extract = $this->createExtractMethodSignature();
        $extract->setBody($this->createExtractMethodBody($classMetadata));

        $hydrate = $this->createHydrateMethodSignature();
        $exposedKeys = $this->createExposedKeysMethod();
        $reflectionProperty = $this->createReflectionPropertyMethod($hierarchy);

        $class->setMethods([$extract, $hydrate, $exposedKeys, $reflectionProperty]);

        return (string) $phpFile;
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
    throw new \InvalidArgumentException(sprintf('%s is able to serialize only instances of "%s" only. "%s" given', get_class(\$this), {$classMetadata->name}::class, is_object(\$object) ? get_class(\$object) : gettype(\$object)));
}

STRING;

        if (!$classMetadata->hasProperties()) {
            $body .= 'return [];';
        }

        $body .= <<<STRING
\$data = [];
\$shouldSerializeNull = \$context->shouldSerializeNull();

STRING;

        /** @var PropertyMetadata $property */
        foreach ($classMetadata->propertyMetadata as $property) {
            if ($property->getter) {
                $body .= $this->createReadCodeFromAccessor($property, $property->getter);
                continue;
            }
            $body .= $this->createReadCodeFromReflection($property);
        }

        /** @var VirtualPropertyMetadata $property */
        foreach ($classMetadata->methodMetadata as $property) {
            $body .= $this->createReadCodeFromAccessor($property, $property->name);
        }

        $body .= <<<STRING
static \$contextKeys = [];
\$contextId = spl_object_hash(\$context);

if (!isset(\$contextKeys[\$contextId])) {
    \$contextKeys[\$contextId] = \$this->getExposedKeys(\$context);
}

\$data = array_intersect_key(\$data, \$contextKeys[\$contextId]);

return \$data;
STRING;

        return $body;
    }

    /**
     * @param PropertyMetadata|VirtualPropertyMetadata $property
     * @param string $accessor
     * @return string
     */
    private function createReadCodeFromAccessor($property, string $accessor): string
    {
        $exposeAs = $property->exposeAs;
        $code = <<<STRING
if (null !== \$value = \$object->{$accessor}()) {

STRING;
        if ($property->readValueFilter) {
            $code .= sprintf('    $data[\'%s\'] = %s;', $exposeAs, $property->readValueFilter);
        } elseif ($property->isScalarType()) {
            $code .= sprintf('    $data[\'%s\'] = (%s) $value;', $exposeAs, $property->type);
        } else {
            $code .= sprintf('    $data[\'%s\'] = $this->serializer->normalize($value, $context);', $exposeAs);
        }

        $code .= <<<STRING

} elseif (\$shouldSerializeNull) {
    \$data['$exposeAs'] = null;
}

STRING;

        return $code;
    }

    private function createReadCodeFromReflection(PropertyMetadata $property): string
    {
        $exposeAs = $property->exposeAs;
        $code = <<<STRING
\$propReflection = \$this->getReflectionProperty({$property->reflection->getDeclaringClass()->getShortName()}::class, '{$property->name}');
\$propReflection->setAccessible(true);
if (null !== \$value = \$propReflection->getValue(\$object)) {

STRING;
        if ($property->readValueFilter) {
            $code .= sprintf('    $data[\'%s\'] = %s;', $exposeAs, $property->readValueFilter);
        } elseif ($property->isScalarType()) {
            $code .= sprintf('    $data[\'%s\'] = (%s) $value;', $exposeAs, $property->type);
        } else {
            $code .= sprintf('    $data[\'%s\'] = $this->serializer->normalize($value, $context);', $exposeAs);
        }

        $code .= <<<STRING

} elseif (\$shouldSerializeNull) {
    \$data['$exposeAs'] = null;
}

STRING;

        return $code;
    }

    private function createHydrateMethodSignature(): Method
    {
        $hydrate = (new Method('hydrate'))
            ->setReturnType('void')
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

    private function createExposedKeysMethod(): Method
    {
        $method = (new Method('getExposedKeys'))
            ->setVisibility('private')
            ->setReturnType('array')
            ->setBody(<<<STRING
\$contextGroups = \$context->getGroups();
\$exposedGroups = array_intersect_key(\$this->exposedGroups, \$contextGroups);
\$exposedKeys = array_reduce(\$exposedGroups, function (\$keys, \$groupKeys) {
    array_push(\$keys, ...(array_keys(\$groupKeys)));
    return \$keys;
}, []);

return array_flip(\$exposedKeys);
STRING
            );

        $method
            ->addParameter('context')
            ->setTypeHint(AbstractContext::class);

        return $method;
    }

    private function createReflectionPropertyMethod(array $hierarchyClasses): Method
    {
        $classes = join(',', array_map(function (string $class): string {
            return sprintf("'%s' => new \ReflectionClass('%s')\n", $class, $class);
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
