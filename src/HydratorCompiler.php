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
     * @var HydratorCodeGenerator
     */
    private $generator;

    /**
     * @var HydratorCodeWriter
     */
    private $writer;

    /**
     * HydratorCompiler constructor.
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
