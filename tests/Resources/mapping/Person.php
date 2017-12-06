<?php

use Tests\TSantos\Serializer\AbstractSerializerClass;
use Tests\TSantos\Serializer\Fixture\Person;

return [
    Person::class => [
        'baseClass' => AbstractSerializerClass::class,
        'properties' => [
            'id' => [
                'type' => 'integer'
            ],
            'name' => [
                'type' => 'string',
                'exposeAs' => 'nome'
            ],
            'married' => [
                'getter' => 'isMarried',
                'type' => 'boolean'
            ],
            'birthday' => [
                'type' => DateTime::class,
                'modifier' => 'format(\'d/m/Y\')'
            ],
            'father' => [
                'type' => Person::class
            ]
        ],
        'virtualProperties' => [
            'fullName' => [
                'exposeAs' => 'full_name'
            ]
        ]
]
];
