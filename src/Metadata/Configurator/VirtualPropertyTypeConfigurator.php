<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Serializer\Metadata\Configurator;

use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\Metadata\ConfiguratorInterface;

/**
 * Class VirtualPropertyTypeConfigurator
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class VirtualPropertyTypeConfigurator implements ConfiguratorInterface
{
    public function configure(ClassMetadata $classMetadata): void
    {
        foreach ($classMetadata->methodMetadata as $virtualPropertyMetadata) {
            if (null !== $virtualPropertyMetadata->type) {
                continue;
            }

            $virtualPropertyMetadata->type = $this->extract($virtualPropertyMetadata->reflection);
        }
    }

    private function extract(\ReflectionMethod $getter): ?string
    {
        // try to read from its return type
        if (null !== $returnType = $getter->getReturnType()) {
            return $this->translate($returnType->getName());
        }

        // try to read from its doc-block return type
        if (null !== $returnType = $this->extractFromDocBlock($getter)) {
            return $this->translate($returnType);
        }

        return null;
    }

    private function extractFromDocBlock(\ReflectionMethod $getter): ?string
    {
        $docBlock = $getter->getDocComment();

        if (is_string($docBlock)) {
            return $this->extractFromDocComment($docBlock);
        }

        return null;
    }

    private function extractFromDocComment(string $docComment): ?string
    {
        if (preg_match('/@(return|var)\s+([^\s]+)/', $docComment, $matches)) {
            list(,, $type) = $matches;
            return $type;
        }

        return null;
    }

    private function translate(string $type): string
    {
        switch ($type) {
            case 'int':
                return 'integer';
            case 'bool':
                return 'boolean';
            case 'mixed':
                return 'string';
            default:
                return $type;
        }
    }
}
