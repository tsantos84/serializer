<?php

require __DIR__ . '/../vendor/autoload.php';

use Serializer\Builder;
use Serializer\Metadata\Driver\ArrayDriver;
use Tests\Serializer\Fixture\Person;

$builder = new Builder();

$builder
    ->setMetadataDriver(new ArrayDriver(require __DIR__ . '/../tests/Resources/mapping.php'))
    ->setCacheDir(__DIR__ . '/../tests/cache');

$serializer = $builder->build();

$person = new Person();
$person->setId(10);
$person->setName('Tales');
$person->setLastName('Santos');
$person->setMarried(true);

$json = $serializer->serialize($person, 'json');

echo $json;
