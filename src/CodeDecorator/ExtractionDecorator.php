<?php

declare(strict_types=1);

/*
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
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\Metadata\PropertyMetadata;
use TSantos\Serializer\Metadata\VirtualPropertyMetadata;
use TSantos\Serializer\SerializationContext;

/**
 * Class ExtractionDecorator.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class ExtractionDecorator implements CodeDecoratorInterface
{
    public function decorate(PhpFile $file, PhpNamespace $namespace, ClassType $class, ClassMetadata $classMetadata): void
    {
        $extract = $class->addMethod('extract')
            ->setReturnType('array')
            ->setVisibility('public')
            ->addComment('@param \\'.$classMetadata->name.' $object')
            ->addComment('@param \\'.SerializationContext::class.' $context')
            ->addComment('@return array')
        ;

        $extract
            ->addParameter('object');

        $extract
            ->addParameter('context')
            ->setTypeHint(SerializationContext::class);

        $extract
            ->setBody($this->createExtractMethodBody($classMetadata));
    }

    private function createExtractMethodBody(ClassMetadata $classMetadata): string
    {
        $discriminatorField = $classMetadata->discriminatorField;

        if (!$classMetadata->hasProperties() && null === $discriminatorField) {
            return 'return [];';
        }

        $data = [];
        foreach ($classMetadata->all() as $property) {
            $data[$property->exposeAs] = null;
        }

        if ($discriminatorField) {
            $values = \array_flip($classMetadata->discriminatorMapping);
            if (!isset($values[$classMetadata->name])) {
                throw new \InvalidArgumentException(implode(',', array_keys($values)));
            }
            $data[$discriminatorField] = $values[$classMetadata->name];
        }

        $body = \sprintf('$data = %s;', \var_export($data, true)).PHP_EOL.PHP_EOL;

        $body .= <<<STRING
{accessors}
if (null === \$groups = \$context->getGroups()) {
    return \$data;
}

\$contextId = \$context->getId();
if (!isset(self::\$exposedPropertiesForContext[\$contextId])) {
    self::\$exposedPropertiesForContext[\$contextId] = \$this->getExposedKeys(\$context);
}
\$data = \array_intersect_key(\$data, self::\$exposedPropertiesForContext[\$contextId]);

return \$data;
STRING;
        $accessors = '';

        /** @var PropertyMetadata $property */
        foreach ($classMetadata->propertyMetadata as $property) {
            if ($property->getter) {
                $accessors .= $this->createAccessorCode($property, '$object->'.$property->getter.'()');
                continue;
            }

            $propCode = $this->createAccessorCode($property, '$this->classMetadata->propertyMetadata[\'{propertyName}\']->reflection->getValue($object)');

            $accessors .= \strtr($propCode, [
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
}

STRING;

        if ($property->readValueFilter) {
            $formatter = \sprintf('$data[\'%s\'] = %s;', $property->exposeAs, $property->readValueFilter);
        } elseif ($property->isScalarType()) {
            $formatter = \sprintf('$data[\'%s\'] = (%s) $value;', $property->exposeAs, $property->type);
        } elseif ($property->isMixedCollectionType()) {
            $formatter = \sprintf('$data[\'%s\'] = $value;', $property->exposeAs, $property->type);
        } elseif ($property->isCollection()) {
            $template = <<<STRING
foreach (\$value as \$key => \$val) {
        \$value[\$key] = {reader};
    }
    \$data['{exposeAs}'] = \$value;
STRING;
            if ($property->isScalarCollectionType()) {
                $reader = \sprintf('(%s) $val', $property->getTypeOfCollection());
            } else {
                $reader = '$this->serializer->normalize($val, $context);';
            }

            $formatter = \strtr($template, [
                '{reader}' => $reader,
                '{exposeAs}' => $property->exposeAs,
            ]);
        } else {
            $formatter = \sprintf('$data[\'%s\'] = $this->serializer->normalize($value, $context);', $property->exposeAs);
        }

        $replaces = [
            '{exposeAs}' => $property->exposeAs,
            '{propertyName}' => $property->name,
            '{formatter}' => $formatter,
            '{accessor}' => $accessor,
        ];

        return \strtr($code, $replaces);
    }
}
