<?php

$serializer = require 'setup.php';

use Tests\TSantos\Serializer\Fixture\Person;

$person = new Person(1, 'Tales Santos', true);
$person->setId(10);
$person->setName('Tales');
$person->setLastName('Santos');
$person->setMarried(true);

$json = $serializer->serialize($person, 'json');

echo $json;
