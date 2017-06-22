<?php

require __DIR__ . '/../vendor/autoload.php';

use Serializer\SerializerBuilder;
use Serializer\Metadata\Driver\ArrayDriver;
use Tests\Serializer\Fixture\Person;

$builder = new SerializerBuilder();

$serializer = $builder
    ->setMetadataDriver(new ArrayDriver(require __DIR__ . '/../tests/Resources/mapping.php'))
    ->setCacheDir(__DIR__ . '/../tests/cache')
    ->setDebug(true)
    ->build();

$persons = [];

for ($i=1; $i<=10; $i++) {
    $person = new Person();
    $person->setId(10);
    $person->setName('Tales');
    $person->setLastName('Santos');
    $person->setMarried(true);
    $persons[] = $person;
}

$json = $serializer->serialize($persons, 'json');

echo $json;
