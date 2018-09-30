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
use Nette\PhpGenerator\Method;
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
    /**
     * @var Template
     */
    private $template;
    /**
     * @var bool
     */
    private $propertyGroupingEnabled;

    /**
     * HydrationDecorator constructor.
     *
     * @param Template $template
     * @param bool     $propertyGroupEnabled
     */
    public function __construct(Template $template, bool $propertyGroupEnabled)
    {
        $this->template = $template;
        $this->propertyGroupingEnabled = $propertyGroupEnabled;
    }

    public function decorate(PhpFile $file, PhpNamespace $namespace, ClassType $class, ClassMetadata $classMetadata): void
    {
        $hydrate = $class->addMethod('hydrate')
            ->setVisibility('public')
            ->addComment('@param \\'.$classMetadata->name.' $object')
            ->addComment('@param array $data')
            ->addComment('@param \\'.DeserializationContext::class.' $context')
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

        $this->appendMethodBody($classMetadata, $hydrate);
    }

    private function appendMethodBody(ClassMetadata $classMetadata, Method $method): void
    {
        if ($classMetadata->isAbstract()) {
            $method->addBody(<<<STRING
// hydrate with concrete hydrator
\$type = \array_search(\$data['{$classMetadata->discriminatorField}'], self::\$discriminatorMapping);
\$this->hydratorLoader->load(\$type)->hydrate(\$object, \$data, \$context);

STRING
            );
        }

        /** @var PropertyMetadata[] $properties */
        $properties = $classMetadata->getWritableProperties();

        if (0 === \count($properties)) {
            $method->addBody('return $object;');

            return;
        }

        if ($this->propertyGroupingEnabled) {
            $method->addBody($this->template->renderGroupHandler());
        }

        /** @var PropertyMetadata[] $properties */
        $properties = $classMetadata->getWritableProperties();

        foreach ($properties as $property) {
            if ($property->setter) {
                $mutator = \sprintf('$object->%s($value);', $property->setter);
            } elseif ($property->reflection->isPublic()) {
                $mutator = \sprintf('$object->%s = $value;', $property->name);
            } else {
                $mutator = \sprintf('$this->classMetadata->propertyMetadata[\'%s\']->reflection->setValue($object, $value);', $property->name);
            }

            $method->addBody($this->template->renderValueWriter($property, $mutator));
        }

        $method->addBody('return $object;');
    }
}
