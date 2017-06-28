<?php

namespace Tests\TSantos\Serializer;

use PHPUnit\Framework\TestCase;
use TSantos\Serializer\Metadata\Driver\ArrayDriver;
use TSantos\Serializer\SerializerBuilder;
use TSantos\Serializer\SerializerInterface;

/**
 * Class SerializerTestCase
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
abstract class SerializerTestCase extends TestCase
{
    private $cacheDir;

    protected function setUp()
    {
        $this->cacheDir = sys_get_temp_dir() . '/serializer/cache';
    }

    protected function tearDown()
    {
        system('rm -rf ' . escapeshellarg($this->cacheDir), $retval);
    }

    protected function createSerializer(array $mapping = []): SerializerInterface
    {
        $builder = new SerializerBuilder();

        $builder
            ->setMetadataDriver(new ArrayDriver($mapping))
            ->setCacheDir($this->cacheDir)
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
