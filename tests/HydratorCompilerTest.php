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

namespace Tests\TSantos\Serializer;

use PHPUnit\Framework\TestCase;
use TSantos\Serializer\HydratorCodeGenerator;
use TSantos\Serializer\HydratorCodeWriter;
use TSantos\Serializer\HydratorCompiler;
use TSantos\Serializer\Metadata\ClassMetadata;

/**
 * Class HydratorCompilerTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class HydratorCompilerTest extends TestCase
{
    /** @test */
    public function it_can_compile_a_class()
    {
        $classMetadata = $this->createMock(ClassMetadata::class);

        $generator = $this->createMock(HydratorCodeGenerator::class);
        $generator
            ->expects($this->once())
            ->method('generate')
            ->with($classMetadata)
            ->willReturn('<?php MyHydrator {}');

        $writer = $this->createMock(HydratorCodeWriter::class);
        $writer
            ->expects($this->once())
            ->method('write')
            ->with($classMetadata, '<?php MyHydrator {}');

        $compiler = new HydratorCompiler($generator, $writer);
        $compiler->compile($classMetadata);
    }
}
