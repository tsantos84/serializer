<?php

require __DIR__ . '/../vendor/autoload.php';

use TSantos\Serializer\SerializerBuilder;

$builder = new SerializerBuilder();

$serializer = $builder
    ->addMetadataDir('Tests\TSantos\Serializer\Fixture', __DIR__ . '/../tests/Resources/mapping')
    ->setDebug(true)
    ->build();

return $serializer;
