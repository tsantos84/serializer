# TSantos Serializer [![Build Status](https://travis-ci.org/tsantos84/serializer.svg?branch=master)](https://travis-ci.org/tsantos84/serializer)

TSantos Serializer is a library to encode/decode PHP objects to some format. Because of its excluive 
serialization strategy, this library is the faster serialization component to PHP.

## Instalation

You can install this library through composer:

`composer require tsantos/serializer`

or just add `tsantos/serializer` to your composer file and then

`composer update`

## Usage

The best way to get start with `TSantos Serializer` is by using the builder.
With a few configurations you are ready to serialize your data:

```php

use TSantos\Serializer\SerializerBuilder;

class Post {
    public $title;
    public $summary;
}

$serializer = (new SerializerBuilder())->build();
$person = new Post('Post title', 'Post summary');
echo $serializer->serialize($person); // {"title":"Post title", "summary":"Post summary"}
```

This is the simplest example to get you started with TSantos Serializer. There are
a lot of capabilities which you should know in order to master your serializer instance
and take advantage of all library's power.

## Features

Main features currently supported by TSantos Serializer:

* Supports `YAML` and `XML` and `Annotations` mapping formats
* Supports `JSON` encoders (output)
* (De-)serializes objects of any depth
* Custom `getters` and `setters`
* Virtual properties
* Properties grouping
* Event listeners to hook into serialization operations

## Documentation

Please refer to the [documentation page](https://tsantos-serializer.readthedocs.io) to see all allowed configurations.

## Licence

MIT

## Tests

  `vendor/bin/phpunit -c phpunit.xml.dist`
