<?php

require __DIR__ . '/../vendor/autoload.php';

use Metadata\Driver\FileLocator;
use TSantos\Serializer\Metadata\Driver\PhpDriver;
use TSantos\Serializer\SerializerBuilder;
use TSantos\Serializer\TypeGuesser;

$builder = new SerializerBuilder();

$fileLocator = new FileLocator([
    'Tests\TSantos\Serializer\Fixture' => __DIR__ . '/../tests/Resources/mapping'
]);
$typeGuesser = new TypeGuesser();

$serializer = $builder
    ->setMetadataDriver(new PhpDriver($fileLocator, $typeGuesser))
    ->setCacheDir(__DIR__ . '/../tests/cache')
    ->setDebug(true)
    ->build();

return $serializer;
