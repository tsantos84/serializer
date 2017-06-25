<?php

$serializer = require 'setup.php';

use Tests\TSantos\Serializer\Fixture\Person;

$persons = [];

for ($i=1; $i<=10; $i++) {
    $person = new Person();
    $person->setId(10);
    $person->setName('Tales');
    $person->setMarried(true);
    $persons[] = $person;
}

$json = $serializer->serialize($persons, 'json');

echo $json;
