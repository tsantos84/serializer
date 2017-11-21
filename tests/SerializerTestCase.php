<?php

namespace Tests\TSantos\Serializer;

use PHPUnit\Framework\TestCase;
use TSantos\Serializer\Metadata\Driver\InMemoryDriver;
use TSantos\Serializer\SerializerBuilder;
use TSantos\Serializer\SerializerInterface;
use TSantos\Serializer\TypeGuesser;

/**
 * Class SerializerTestCase
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
abstract class SerializerTestCase extends TestCase
{
    protected $cacheDir;

    protected function setUp()
    {
        $this->cacheDir = sys_get_temp_dir() . '/serializer/cache';
    }

    protected function tearDown()
    {
        system('rm -rf ' . escapeshellarg($this->cacheDir), $retval);
    }

    /**
     * @return SerializerBuilder
     */
    protected function createBuilder()
    {
        return new SerializerBuilder();
    }

    protected function createSerializer(array $mapping = []): SerializerInterface
    {
        $builder = $this->createBuilder();

        $builder
            ->setMetadataDriver(new InMemoryDriver($mapping, new TypeGuesser()))
            ->setSerializerClassDir($this->cacheDir)
            ->setDebug(true);

        return $builder->build();
    }

    protected function createMapping(string $type, array $properties, array $virtualProperties = []): array
    {
        return [
            $type => [
                'properties' => $properties,
                'virtual_properties' => $virtualProperties
            ]
        ];
    }
}
