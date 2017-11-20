<?php

$serializer = require 'setup.php';

use TSantos\Serializer\SerializationContext;
use Tests\TSantos\Serializer\Fixture\Person;

$person = new Person(1, 'Tales Santos', true);

$json = $serializer->serialize($person, 'json', SerializationContext::create()->setGroups(['web']));

echo $json;
