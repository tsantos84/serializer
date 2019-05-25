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

namespace TSantos\Serializer\Metadata\Driver;

use Metadata\ClassMetadata as JmsClassMetadata;
use Metadata\Driver\AbstractFileDriver;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\Metadata\PropertyMetadata;
use TSantos\Serializer\Metadata\VirtualPropertyMetadata;

/**
 * Class XmlDriver.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class XmlDriver extends AbstractFileDriver
{
    protected function loadMetadataFromFile(\ReflectionClass $class, string $file): ?JmsClassMetadata
    {
        $previous = \libxml_use_internal_errors(true);
        \libxml_clear_errors();
        $elem = \simplexml_load_file($file);
        \libxml_use_internal_errors($previous);
        if (false === $elem) {
            throw new \RuntimeException(\libxml_get_last_error());
        }

        $metadata = new ClassMetadata($name = $class->name);

        if (!$elems = $elem->xpath("./class[@name = '".$name."']")) {
            throw new \RuntimeException(\sprintf('Could not find class %s inside XML element.', $name));
        }

        /** @var \SimpleXMLElement $elem */
        $elem = \reset($elems);

        if (null !== $baseClass = $elem->attributes()->{'base-class'}) {
            $metadata->baseClass = (string) $baseClass;
        }

        if (\count($discriminator = $elem->xpath('./discriminator')) > 0) {
            $discriminatorElem = \current($discriminator);
            $field = (string) $discriminatorElem->attributes()->{'field'};
            $mapping = [];
            foreach ($discriminatorElem->xpath('./map') as $map) {
                $value = (string) $map->attributes()->{'value'};
                $mapping[$value] = (string) $map;
            }
            $metadata->setDiscriminatorMap($field, $mapping);
        }

        if (\count($hydratorArgs = $elem->xpath('./hydrator_construct_args')) > 0) {
            foreach (\current($hydratorArgs)->xpath('./arg') as $arg) {
                $name = (string) $arg->attributes()->{'name'};
                $metadata->hydratorConstructArgs[$name] = (string) $arg;
            }
        }

        /* @var \SimpleXMLElement $property */
        foreach ($elem->xpath('./property') as $xmlProperty) {
            $attribs = ((array) $xmlProperty->attributes())['@attributes'];
            $property = new PropertyMetadata($class->getName(), (string) $attribs['name']);

            if (isset($attribs['expose-as'])) {
                $property->exposeAs = $attribs['expose-as'];
            }

            if (isset($attribs['getter'])) {
                $property->setGetter($attribs['getter']);
            }

            if (isset($attribs['setter'])) {
                $property->setGetter($attribs['setter']);
            }

            /** @var \SimpleXMLElement[] $options */
            if (\count($options = $xmlProperty->xpath('./options/option'))) {
                $o = [];
                foreach ($options as $v) {
                    $o[(string) $v['name']] = (string) $v;
                }
                $property->options = $o;
            }

            if (isset($attribs['groups'])) {
                $property->groups = \preg_split('/\s*,\s*/', \trim((string) $attribs['groups']));
            } elseif (isset($xmlProperty->groups)) {
                $property->groups = (array) $xmlProperty->groups->value;
            }

            $property->readValueFilter = $attribs['read-value-filter'] ?? null;
            $property->writeValueFilter = $attribs['write-value-filter'] ?? null;
            $property->type = $attribs['type'] ?? null;
            $property->readOnly = 'true' === \mb_strtolower($attribs['read-only'] ?? '') ?? false;

            $metadata->addPropertyMetadata($property);
        }

        /* @var \SimpleXMLElement $property */
        foreach ($elem->xpath('./virtual_property') ?? [] as $xmlProperty) {
            $attribs = ((array) $xmlProperty->attributes())['@attributes'];
            $name = $attribs['name'];
            $method = $attribs['method'] ?? $name;

            $property = new VirtualPropertyMetadata($class->name, $method);
            $property->type = $attribs['type'] ?? null;
            $property->exposeAs = $attribs['expose-as'] ?? $name;
            $property->readValueFilter = $attribs['read-value'] ?? null;

            if (isset($attribs['groups'])) {
                $property->groups = \preg_split('/\s*,\s*/', \trim((string) $attribs['groups']));
            } elseif (isset($xmlProperty->groups)) {
                $property->groups = (array) $xmlProperty->groups->value;
            }

            /** @var \SimpleXMLElement[] $options */
            if (\count($options = $xmlProperty->xpath('./options/option'))) {
                $o = [];
                foreach ($options as $v) {
                    $o[(string) $v['name']] = (string) $v;
                }
                $property->options = $o;
            }

            $metadata->addMethodMetadata($property);
        }

        return $metadata;
    }

    protected function getExtension(): string
    {
        return 'xml';
    }
}
