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

use Metadata\MetadataFactoryInterface;
use PHPUnit\Framework\TestCase;
use TSantos\Serializer\Configuration;
use TSantos\Serializer\Exception\MappingException;
use TSantos\Serializer\HydratorCompilerInterface;
use TSantos\Serializer\HydratorFactoryInterface;
use TSantos\Serializer\HydratorLoader;

class HydratorLoaderTest extends TestCase
{
    /** @var HydratorLoader */
    private $loader;
    private $configuration;
    private $metadata;
    private $compiler;
    private $factory;

    protected function setUp(): void
    {
        $this->configuration = $this->createMock(Configuration::class);
        $this->metadata = $this->createMock(MetadataFactoryInterface::class);
        $this->compiler = $this->createMock(HydratorCompilerInterface::class);
        $this->factory = $this->createMock(HydratorFactoryInterface::class);
        $this->loader = new HydratorLoader($this->configuration, $this->metadata, $this->compiler, $this->factory);
    }

    /** @test */
    public function it_should_throw_exception_if_the_metadata_cant_be_found()
    {
        $this->expectException(MappingException::class);
        $this->loader->load('stdClass');
    }
}
