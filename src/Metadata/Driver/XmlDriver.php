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

use Metadata\Driver\AbstractFileDriver;
use Metadata\Driver\FileLocatorInterface;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\Metadata\PropertyMetadata;
use TSantos\Serializer\Metadata\VirtualPropertyMetadata;
use TSantos\Serializer\TypeGuesser;

/**
 * Class XmlDriver
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class XmlDriver extends AbstractFileDriver
{
    /**
     * @var TypeGuesser
     */
    private $typeGuesser;

    /**
     * XmlDriver constructor.
     * @param FileLocatorInterface $locator
     * @param TypeGuesser $typeGuesser
     */
    public function __construct(FileLocatorInterface $locator, TypeGuesser $typeGuesser)
    {
        $this->typeGuesser = $typeGuesser;
        parent::__construct($locator);
    }

    protected function loadMetadataFromFile(\ReflectionClass $class, $file)
    {
        $previous = libxml_use_internal_errors(true);
        libxml_clear_errors();
        $elem = simplexml_load_file($file);
        libxml_use_internal_errors($previous);
        if (false === $elem) {
            throw new \RuntimeException(libxml_get_last_error());
        }

        $metadata = new ClassMetadata($name = $class->name);

        if (!$elems = $elem->xpath("./class[@name = '" . $name . "']")) {
            throw new \RuntimeException(sprintf('Could not find class %s inside XML element.', $name));
        }

        /** @var \SimpleXMLElement $elem */
        $elem = reset($elems);

        if (null !== $baseClass = $elem->attributes()->{'base-class'}) {
            $metadata->baseClass = $baseClass;
        }

        /** @var \SimpleXMLElement $property */
        foreach ($elem->xpath('./property') as $xmlProperty) {
            $attribs = ((array)$xmlProperty->attributes())['@attributes'];
            $name = (string)$attribs['name'];
            $property = new PropertyMetadata($class->getName(), $name);

            if ($class->hasMethod($getter = $attribs['getter'] ?? 'get' . ucfirst($name))) {
                $property->getter = $getter;
                $property->getterRef = new \ReflectionMethod($class->getName(), $property->getter);
            }

            if ($class->hasMethod($setter = $attribs['setter'] ?? 'set' . ucfirst($name))) {
                $property->setter = $setter;
                $property->setterRef = new \ReflectionMethod($class->getName(), $setter);
            }

            if (isset($attribs['groups'])) {
                $property->groups = preg_split('/\s*,\s*/', trim((string)$attribs['groups']));
            } elseif (isset($xmlProperty->groups)) {
                $property->groups = (array)$xmlProperty->groups->value;
            }

            $property->modifier = $attribs['modifier'] ?? null;
            $property->type = $attribs['type'] ?? $this->typeGuesser->guessProperty($property, 'string');
            $property->exposeAs = $attribs['expose-as'] ?? $name;
            $property->readOnly = strtolower($attribs['read-only'] ?? '') === 'true' ?? false;

            $metadata->addPropertyMetadata($property);
        }

        /** @var \SimpleXMLElement $property */
        foreach ($elem->xpath('./virtual_property') ?? [] as $xmlProperty) {
            $attribs = ((array)$xmlProperty->attributes())['@attributes'];
            $name = $attribs['name'];
            $method = $attribs['method'] ?? $name;

            $property = new VirtualPropertyMetadata($class->name, $method);
            $property->type = $attribs['type'] ?? $this->typeGuesser->guessVirtualProperty($property, 'string');
            $property->exposeAs = $attribs['expose-as'] ?? $name;
            $property->modifier = $attribs['modifier'] ?? null;

            if (isset($attribs['groups'])) {
                $property->groups = preg_split('/\s*,\s*/', trim((string)$attribs['groups']));
            } elseif (isset($xmlProperty->groups)) {
                $property->groups = (array)$xmlProperty->groups->value;
            }

            $metadata->addMethodMetadata($property);
        }

        return $metadata;
    }

    protected function getExtension()
    {
        return 'xml';
    }
}
