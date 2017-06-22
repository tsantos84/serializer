<?php

use Tests\Serializer\Fixture\Address;
use Tests\Serializer\Fixture\Coordinates;
use Tests\Serializer\Fixture\Person;

return [
    Person::class => [
        'properties' => [
            'id' => [
                'type' => 'integer'
            ],
            'name' => [
                'type' => 'string',
                'exposeAs' => 'nome'
            ],
            'lastName' => [
                'type' => 'string',
                'exposeAs' => 'last_name'
            ],
            'married' => [
                'type' => 'boolean',
                'getter' => 'isMarried'
            ],
            'colors' => [
                'type' => 'array'
            ],
            'address' => [
                'type' => Address::class
            ]
        ]
    ],
    Address::class => [
        'properties' => [
            'street' => [],
            'city' => [],
            'coordinates' => [
                'type' => Coordinates::class
            ]
        ]
    ],
    Coordinates::class => [
        'properties' => [
            'x' => [
                'type' => 'float'
            ],
            'y' => [
                'type' => 'float'
            ]
        ]
    ]
];
