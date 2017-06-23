<?php

use Tests\Serializer\Fixture\Address;
use Tests\Serializer\Fixture\Coordinates;
use Tests\Serializer\Fixture\Person;

return [
    Person::class => [
        'properties' => [
            'id' => [
                'type' => 'integer',
                'groups' => ['web']
            ],
            'name' => [
                'type' => 'string',
                'exposeAs' => 'nome'
            ],
            'lastName' => [
                'type' => 'string',
                'exposeAs' => 'last_name',
                'groups' => ['web']
            ],
            'married' => [
                'type' => 'boolean',
                'getter' => 'isMarried'
            ],
            'colors' => [
                'type' => 'array',
                'groups' => ['mobile']
            ],
            'address' => [
                'type' => Address::class
            ]
        ],
        'virtual_properties' => [
            'fullName' => [
                'exposeAs' => 'full_name'
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
