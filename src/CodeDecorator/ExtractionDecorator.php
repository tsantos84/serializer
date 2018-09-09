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
    /**
     * @var Template
     */
    private $template;

    /**
     * ExtractionDecorator constructor.
     *
     * @param Template $template
     */
    public function __construct(Template $template)
    {
        $this->template = $template;
    }

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

        $this->configureExtractMethodBody($classMetadata, $extract);
    }

    private function configureExtractMethodBody(ClassMetadata $classMetadata, Method $method): void
    {
        $discriminatorField = $classMetadata->discriminatorField;

        if (!$classMetadata->hasProperties() && null === $discriminatorField) {
            $method->addBody('return [];');

            return;
        }

        $data = [];
        foreach ($classMetadata->all() as $property) {
            $data[$property->exposeAs] = null;
        }

        if (!$classMetadata->isAbstract() && $discriminatorField) {
            $values = \array_flip($classMetadata->discriminatorMapping);
            $data[$discriminatorField] = $values[$classMetadata->name];
        }

        $method->addBody('$data = ?;', [$data]);
        $method->addBody(PHP_EOL);

        /** @var PropertyMetadata $property */
        foreach ($classMetadata->propertyMetadata as $property) {
            $method->addBody($this->template->renderValueReader($property));
        }

        /** @var VirtualPropertyMetadata $property */
        foreach ($classMetadata->methodMetadata as $property) {
            $method->addBody($this->template->renderValueReader($property));
        }

        $method->addBody($this->template->renderGroupHandler());

        $method->addBody('return $data;');
    }
}
