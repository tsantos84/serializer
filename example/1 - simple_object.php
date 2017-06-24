<?php

$serializer = require 'setup.php';

use Tests\Serializer\Fixture\Person;

$person = new Person();
$person->setId(10);
$person->setName('Tales');
$person->setLastName('Santos');
$person->setMarried(true);

$json = $serializer->serialize($person, 'json');

echo $json;
