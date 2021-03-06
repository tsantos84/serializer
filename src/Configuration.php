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

namespace TSantos\Serializer;

use TSantos\Serializer\Metadata\ClassMetadata;

/**
 * Class Configuration.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class Configuration
{
    /**
     * @var string
     */
    private $hydratorNamespace;

    /**
     * @var string
     */
    private $hydratorDirectory;

    /**
     * @var int
     */
    private $generationStrategy;

    /**
     * @var bool
     */
    private $maxDepthCheck;

    /**
     * Configuration constructor.
     *
     * @param string $hydratorNamespace
     * @param string $hydratorDirectory
     * @param int    $generationStrategy
     * @param bool   $enableMaxDepthCheck
     */
    public function __construct(string $hydratorNamespace, string $hydratorDirectory, int $generationStrategy, bool $maxDepthCheck)
    {
        $this->hydratorNamespace = $hydratorNamespace;
        $this->hydratorDirectory = $hydratorDirectory;
        $this->generationStrategy = $generationStrategy;
        $this->maxDepthCheck = $maxDepthCheck;
    }

    /**
     * @return string
     */
    public function getHydratorNamespace(): string
    {
        return $this->hydratorNamespace;
    }

    /**
     * @return string
     */
    public function getHydratorDirectory(): string
    {
        return $this->hydratorDirectory;
    }

    /**
     * @return int
     */
    public function getGenerationStrategy(): int
    {
        return $this->generationStrategy;
    }

    /**
     * @param ClassMetadata $classMetadata
     *
     * @return string
     */
    public function getNamespaceForClass(ClassMetadata $classMetadata): string
    {
        $namespace = \sprintf('%s\\%s', $this->hydratorNamespace, $classMetadata->reflection->getNamespaceName());

        return \rtrim($namespace, '\\');
    }

    /**
     * @param ClassMetadata $classMetadata
     *
     * @return string
     */
    public function getClassName(ClassMetadata $classMetadata): string
    {
        return $classMetadata->reflection->getShortName().'Hydrator';
    }

    /**
     * @param ClassMetadata $classMetadata
     *
     * @return string
     */
    public function getFqnClassName(ClassMetadata $classMetadata): string
    {
        return $this->getNamespaceForClass($classMetadata).'\\'.$this->getClassName($classMetadata);
    }

    /**
     * @param ClassMetadata $classMetadata
     *
     * @return string
     */
    public function getFilename(ClassMetadata $classMetadata): string
    {
        $filename = \str_replace('\\', '', $classMetadata->reflection->name);

        return $this->hydratorDirectory.\DIRECTORY_SEPARATOR.$filename.'.php';
    }

    public function isMaxDepthCheckEnabled(): bool
    {
        return $this->maxDepthCheck;
    }
}
