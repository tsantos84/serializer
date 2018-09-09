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

/**
 * Class CreateInstanceMethodDecorator.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class NewInstanceMethodDecorator implements CodeDecoratorInterface
{
    /**
     * @var Template
     */
    private $template;

    /**
     * HydrationDecorator constructor.
     *
     * @param Template $template
     */
    public function __construct(Template $template)
    {
        $this->template = $template;
    }

    public function decorate(PhpFile $file, PhpNamespace $namespace, ClassType $class, ClassMetadata $classMetadata): void
    {
        $method = $class
            ->addMethod('newInstance')
            ->addComment('@param array $data')
            ->addComment('@param \\'.DeserializationContext::class.' $context')
            ->addComment('@return \\'.$classMetadata->name)
        ;

        $method
            ->addParameter('data')
            ->setTypeHint('array');

        $method
            ->addParameter('context')
            ->setTypeHint(DeserializationContext::class);

        if ($classMetadata->isAbstract()) {
            $this->configureMethodBodyToUseConcreteHydrator($method, $classMetadata);

            return;
        }

        if ($classMetadata->canBeInstantiatedThroughConstructor()) {
            $this->configureMethodToInstantiateObjectsThroughConstructor($method, $classMetadata);

            return;
        }

        $method->setBody('return $this->instantiator->create(\''.$classMetadata->name.'\', $data, $context);');
    }

    private function configureMethodBodyToUseConcreteHydrator(Method $method, ClassMetadata $classMetadata): void
    {
        $code = <<<STRING
if (!isset(\$data['{$classMetadata->discriminatorField}'])) {
    throw new \InvalidArgumentException('The \$data provided should have the field "{$classMetadata->discriminatorField}"');
}

\$type = \array_search(\$data['{$classMetadata->discriminatorField}'], self::\$discriminatorMapping);
\$hydrator = \$this->hydratorLoader->load(\$type);

return \$hydrator->newInstance(\$data, \$context);
STRING;

        $method->setBody($code);
    }

    private function configureMethodToInstantiateObjectsThroughConstructor(Method $newInstanceMethod, ClassMetadata $classMetadata): void
    {
        $constructor = $classMetadata->reflection->getConstructor();
        $defaultArgs = \array_fill(0, $constructor->getNumberOfParameters(), null);
        $newInstanceMethod->addBody('$args = ?;', [$defaultArgs]);

        foreach ($classMetadata->getConstructProperties() as $prop) {
            $mutator = \sprintf('$args[%d] = $value;', $classMetadata->constructArgs[$prop->name]);
            $newInstanceMethod->addBody($this->template->renderValueWriter($prop, $mutator));
        }

        $newInstanceMethod->addBody('return $this->classMetadata->reflection->newInstanceArgs($args);');
    }
}
