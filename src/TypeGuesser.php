<?php

namespace Serializer;

use Metadata\MethodMetadata;
use Serializer\Metadata\PropertyMetadata;

/**
 * Class TypeGuesser
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class TypeGuesser
{
    /**
     * @param PropertyMetadata $metadata
     * @param string $default
     * @return string
     */
    public function guessProperty(PropertyMetadata $metadata, string $default): string
    {
        $ref = $metadata->reflection;

        if (null !== $type = $this->readFromDocComment($ref->getDocComment())) {
            return $this->translate($type) ?? $default;
        }

        return $this->guessFromMethod($metadata->getterRef, $default);
    }

    /**
     * @param MethodMetadata $metadata
     * @param string $default
     * @return string
     */
    public function guessVirtualProperty(MethodMetadata $metadata, string $default): string
    {
        return $this->guessFromMethod($metadata->reflection, $default);
    }

    /**
     * @param \ReflectionMethod $method
     * @param string $default
     * @return null|string
     */
    private function guessFromMethod(\ReflectionMethod $method, string $default)
    {
        if (null === $returnType = $method->getReturnType()) {
            if (null !== $type = $this->readFromDocComment($method->getDocComment())) {
                return $this->translate($type) ?? $default;
            }
        } elseif ($returnType->isBuiltin()) {
            return $this->translate((string) $returnType) ?? $default;
        }

        return $default;
    }

    /**
     * @param string $docComment
     * @return null|string
     */
    private function readFromDocComment(string $docComment): ?string
    {
        if (preg_match('/@var\s+([^\s]+)/', $docComment, $matches)) {
            list(, $type) = $matches;
            return $type;
        }

        return null;
    }

    /**
     * @param string $type
     * @return null|string
     */
    private function translate(string $type): ?string
    {
        switch ($type) {
            case 'int':
            case 'integer':
                return 'integer';

            case 'string':
                return 'string';

            case 'float':
                return 'float';

            case 'bool':
            case 'boolean':
                return 'boolean';
        }

        return null;
    }
}
