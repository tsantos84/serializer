<?php

require __DIR__ . '/../vendor/autoload.php';

use Serializer\SerializerBuilder;
use Serializer\Metadata\Driver\ArrayDriver;
use Tests\Serializer\Fixture\Address;
use Tests\Serializer\Fixture\Coordinates;
use Tests\Serializer\Fixture\Person;

$builder = new SerializerBuilder();

$serializer = $builder
    ->setMetadataDriver(new ArrayDriver(require __DIR__ . '/../tests/Resources/mapping.php'))
    ->setCacheDir(__DIR__ . '/../tests/cache')
    ->setDebug(true)
    ->build();

$person = new Person();
$person->setId(10);
$person->setName('Tales');
$person->setLastName('Santos');
$person->setMarried(true);

$address = new Address();
$address->setCity('Belo Horizonte');
$address->setStreet('Afonso Pena');
$address->setCoordinates(new Coordinates(10.5, 20.9));
$person->setAddress($address);

$json = $serializer->serialize($person, 'json');

echo $json;
