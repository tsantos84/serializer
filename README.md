# TSantos Serializer
[![Build Status](https://travis-ci.org/tsantos84/serializer.svg?branch=master)](https://travis-ci.org/tsantos84/serializer) [![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=tsantos84_serializer&metric=alert_status)](https://sonarcloud.io/dashboard?id=tsantos84_serializer) [![Coverage](https://sonarcloud.io/api/project_badges/measure?project=tsantos84_serializer&metric=coverage)](https://sonarcloud.io/dashboard?id=tsantos84_serializer) [![Latest Stable Version](https://poser.pugx.org/tsantos/serializer/version)](https://packagist.org/packages/tsantos/serializer) [![Total Downloads](https://poser.pugx.org/tsantos/serializer/downloads)](https://packagist.org/packages/tsantos/serializer) [![Latest Unstable Version](https://poser.pugx.org/tsantos/serializer/v/unstable)](//packagist.org/packages/tsantos/serializer) [![License](https://poser.pugx.org/tsantos/serializer/license)](https://packagist.org/packages/tsantos/serializer) [![composer.lock available](https://poser.pugx.org/tsantos/serializer/composerlock)](https://packagist.org/packages/tsantos/serializer)

TSantos Serializer is a library to encode/decode PHP objects to some string representation. Because of its exclusive
serialization strategy, this library is the [faster serialization component](https://github.com/tsantos84/serializer-benchmark) to PHP.

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

$serializer = (new SerializerBuilder())
    ->setHydratorDir('/path/to/generated/hydrators')
    ->build();

$person = new Post('Post title', 'Post summary');

echo $serializer->serialize($person); // {"title":"Post title", "summary":"Post summary"}
```

This is the simplest example to get you started with TSantos Serializer. There are
a lot of capabilities which you should know in order to master your serializer instance
and take advantage of all library's power.

## Features

Main features currently supported by TSantos Serializer:

* No need to mapping classes for simple use cases ...
* ... but supports `YAML`, `XML` and `Annotations` mapping formats for advanced mapping
* Supports `JSON` encoders
* (De-)serializes objects of any depth
* Virtual properties
* Properties grouping
* Event listeners to hook into serialization operations
* (De-)serializes interfaces and abstract classes

## Documentation

Please refer to the [documentation page](https://tsantos-serializer.readthedocs.io) to see all allowed configurations.

## Licence

MIT

## Tests

  `vendor/bin/phpunit -c phpunit.xml.dist`
