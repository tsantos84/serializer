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

        $context = [
            'target_full_class_name' => $classMetadata->name,
            'class_name' => $this->getClassName($classMetadata),
            'base_class_name' => $classMetadata->baseClass,
            'groups' => $groups,
            'exported_groups' => var_export($groups, true),
            'target_class_name' => $classMetadata->reflection->getShortName(),
            'properties' => $classMetadata->propertyMetadata,
            'virtual_properties' => $classMetadata->methodMetadata,
            'hierarchy_classes' => $hierarchy,
        ];

        return $this->twig->render($classMetadata->template, $context);
    }

    public function getClassName(ClassMetadata $classMetadata): string
    {
        return str_replace('\\', '', $classMetadata->name).'Hydrator';
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
