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

use TSantos\Serializer\Configuration;
use TSantos\Serializer\Exception\UnexpectedTypeException;
use TSantos\Serializer\Metadata\PropertyMetadata;
use TSantos\Serializer\Metadata\VirtualPropertyMetadata;
use TSantos\Serializer\TypeHelper;

/**
 * Class CodeTemplate.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class Template
{
    private static $propertyWriteTemplate = <<<STRING
// property "{propertyName}"
if (isset(\$data['{exposeAs}']) || \array_key_exists('{exposeAs}', \$data)) {
    if (null !== \$value = \$data['{exposeAs}']) {
        {value}
    }
    {mutator}
}

STRING;

    private static $propertyReadTemplate = <<<STRING
// property "{propertyName}"
if (null !== \$value = {accessor}) {
    {exposure}
}

STRING;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * Template constructor.
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function renderTypeChecker(string $type, string $value): string
    {
        $checker = TypeHelper::getChecker($type, $value);
        $exceptionClass = '\\'.UnexpectedTypeException::class;

        $template = <<<STRING
        if (!($checker)) {
                throw {$exceptionClass}::keyType('{$type}', \gettype(\$key));
            }

STRING;

        return $template;
    }

    public function renderValueWriter(PropertyMetadata $property, string $mutator): string
    {
        $code = \strtr(self::$propertyWriteTemplate, [
            '{propertyName}' => $property->name,
            '{exposeAs}' => $property->exposeAs,
            '{value}' => $this->createValueMutator($property),
            '{mutator}' => $mutator,
        ]);

        return $code;
    }

    /**
     * @param PropertyMetadata|VirtualPropertyMetadata $property
     *
     * @return string
     */
    public function renderValueReader($property): string
    {
        if ($property instanceof VirtualPropertyMetadata) {
            $accessor = \sprintf('$object->%s()', $property->name);
        } elseif (null !== $property->getter) {
            $accessor = \sprintf('$object->%s()', $property->getter);
        } elseif ($property->reflection->isPublic()) {
            $accessor = \sprintf('$object->%s', $property->name);
        } else {
            $accessor = \sprintf('$this->classMetadata->propertyMetadata[\'%s\']->reflection->getValue($object)', $property->name);
        }

        return \strtr(self::$propertyReadTemplate, [
            '{propertyName}' => $property->name,
            '{accessor}' => $accessor,
            '{exposure}' => $this->createValueExposure($property),
        ]);
    }

    public function renderGroupHandler()
    {
        return <<<STRING
if (null !== \$context->getGroups()) {
    \$contextId = \$context->getId();
    if (!isset(self::\$exposedPropertiesForContext[\$contextId])) {
        self::\$exposedPropertiesForContext[\$contextId] = static::getExposedKeys(\$context);
    }
    \$data = \array_intersect_key(\$data, self::\$exposedPropertiesForContext[\$contextId]);
}

STRING;
    }

    private function createValueMutator(PropertyMetadata $property): string
    {
        if ($property->writeValueFilter) {
            $mutator = \sprintf('$value = %s;', $property->writeValueFilter);
        } elseif ($property->isScalarType()) {
            $mutator = \sprintf('$value = (%s) $value;', $property->type);
        } elseif ($property->isMixedCollectionType()) {
            $mutator = '$value = (array) $value;';
        } elseif ($property->isCollection()) {
            $template = <<<STRING
foreach (\$value as \$key => \$val) {

STRING;
            if (isset($property->options['key_type'])) {
                $template .= <<<STRING
    {$this->renderTypeChecker($property->options['key_type'], '$key')};

STRING;
            }
            $template .= <<<STRING
            \$value[\$key] = {reader};
        }
STRING;

            if ($property->isScalarCollectionType()) {
                $reader = \sprintf('(%s) $val', $property->getTypeOfCollection());
            } else {
                $reader = \sprintf('$this->serializer->denormalize($val, \'%s\', $context)', $property->getTypeOfCollection());
            }

            $mutator = \strtr($template, [
                '{reader}' => $reader,
                '{setter}' => $property->setter,
                '{exposeAs}' => $property->exposeAs,
            ]);
        } else {
            $mutator = \sprintf('$value = $this->serializer->denormalize($value, \'%s\', $context);', $property->type);
        }

        return $mutator;
    }

    /**
     * @param PropertyMetadata|VirtualPropertyMetadata $property
     *
     * @return string
     */
    private function createValueExposure($property): string
    {
        if ($property->readValueFilter) {
            $exposure = \sprintf('$data[\'%s\'] = %s;', $property->exposeAs, $property->readValueFilter);
        } elseif ($property->isScalarType()) {
            $exposure = \sprintf('$data[\'%s\'] = (%s) $value;', $property->exposeAs, $property->type);
        } elseif ($property->isMixedCollectionType()) {
            $exposure = \sprintf('$data[\'%s\'] = $value;', $property->exposeAs, $property->type);
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

            $exposure = \strtr($template, [
                '{reader}' => $reader,
                '{exposeAs}' => $property->exposeAs,
            ]);
        } else {
            $exposure = \sprintf('$data[\'%s\'] = $this->serializer->normalize($value, $context);', $property->exposeAs);
        }

        if (!$this->configuration->isMaxDepthCheckEnabled()) {
            return $exposure;
        }

        $maxDepthWrap = <<<STRING
if (!\$context->isMaxDepthAchieve(\$this->classMetadata->{locatePropertyAt}['{propertyName}'])) {
        {exposure}
    }
STRING;

        return \strtr($maxDepthWrap, [
            '{exposure}' => $exposure,
            '{propertyName}' => $property->name,
            '{locatePropertyAt}' => $property instanceof PropertyMetadata ? 'propertyMetadata' : 'methodMetadata',
        ]);
    }
}
