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
    /**
     * @deprecated Constant moved to HydratorLoader and will be removed from HydratorCompiler on version 5.0
     * @see \TSantos\Serializer\HydratorLoader::COMPILE_NEVER
     */
    const AUTOGENERATE_NEVER = HydratorLoader::COMPILE_NEVER;

    /**
     * @deprecated Constant moved to HydratorLoader and will be removed from HydratorCompiler on version 5.0
     * @see \TSantos\Serializer\HydratorLoader::COMPILE_ALWAYS
     */
    const AUTOGENERATE_ALWAYS = HydratorLoader::COMPILE_ALWAYS;

    /**
     * @deprecated Constant moved to HydratorLoader and will be removed from HydratorCompiler on version 5.0
     * @see \TSantos\Serializer\HydratorLoader::COMPILE_IF_NOT_EXISTS
     */
    const AUTOGENERATE_FILE_NOT_EXISTS = HydratorLoader::COMPILE_IF_NOT_EXISTS;

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
     * @param HydratorCodeGenerator $generator
     * @param HydratorCodeWriter    $writer
     */
    public function __construct(HydratorCodeGenerator $generator, HydratorCodeWriter $writer)
    {
        $this->generator = $generator;
        $this->writer = $writer;
    }

    public function compile(ClassMetadata $classMetadata): void
    {
        $code = $this->generator->generate($classMetadata);
        $this->writer->write($classMetadata, $code);
    }
}
