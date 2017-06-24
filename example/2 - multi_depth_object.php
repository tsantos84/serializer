<?php

$serializer = require 'setup.php';

use Tests\Serializer\Fixture\Address;
use Tests\Serializer\Fixture\Coordinates;
use Tests\Serializer\Fixture\Person;

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
