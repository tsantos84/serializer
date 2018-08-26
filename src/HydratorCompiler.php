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
 * Class HydratorCompiler.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class HydratorCompiler implements HydratorCompilerInterface
{
    const AUTOGENERATE_NEVER = 1;
    const AUTOGENERATE_ALWAYS = 2;
    const AUTOGENERATE_FILE_NOT_EXISTS = 3;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var HydratorCodeGenerator
     */
    private $generator;

    /**
     * @var HydratorCodeWriter
     */
    private $writer;

    /**
     * Compiler constructor.
     *
     * @param Configuration         $configuration
     * @param HydratorCodeGenerator $generator
     * @param HydratorCodeWriter    $writer
     */
    public function __construct(Configuration $configuration, HydratorCodeGenerator $generator, HydratorCodeWriter $writer)
    {
        $this->configuration = $configuration;
        $this->generator = $generator;
        $this->writer = $writer;
    }

    public function compile(ClassMetadata $classMetadata): void
    {
        $filename = $this->configuration->getFilename($classMetadata);

        switch ($this->configuration->getGenerationStrategy()) {
            case self::AUTOGENERATE_NEVER:
                requireHydrator($filename);
                break;

            case self::AUTOGENERATE_ALWAYS:
                $this->generate($classMetadata);
                requireHydrator($filename);
                break;

            case self::AUTOGENERATE_FILE_NOT_EXISTS:
                if (!\file_exists($filename)) {
                    $this->generate($classMetadata);
                }
                requireHydrator($filename);
                break;
        }
    }

    private function generate(ClassMetadata $classMetadata)
    {
        $code = $this->generator->generate($classMetadata);
        $this->writer->write($classMetadata, $code);
    }
}

function requireHydrator(string $filename): void
{
    /** @noinspection PhpIncludeInspection */
    require_once $filename;
}
