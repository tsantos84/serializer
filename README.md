# TSantos Serializer [![Build Status](https://travis-ci.org/tsantos84/serializer.svg?branch=master)](https://travis-ci.org/tsantos84/serializer)

## Introduction

With the growth of micro-services a good serializer tool should be use to expose your data over the internet. Such tools should be able to read data (commonly PHP objects) and encode it to any format to be returned for some API client. TSantos Serializer was built focused on performance and APIs which requires fast responses, low CPU and low memory usage as non-functional requirements. Moreover, this library tries to do all this proccess without loosing the benefits of data-mapping and flexible configurations.

## Motivation

No, I'm not forgetting the DRY concept and I have good reasons to writing this library. I know that there are most known library that do this job like [JMS Serializer](https://github.com/schmittjoh/serializer) and [Symfony Serializer](http://symfony.com/doc/current/components/serializer.html), but I'm not satisfied with its performance. They have a considerable overhead and in almost time they consume precious miliseconds of RESTful APIs.

I used to use JMS a lot and I really like that tool, but now I want to get my application's performance to a high level without loosing the great feature as that library provides.

Still not convinced? I've created a [benchmark application](https://github.com/tsantos84/serializers-benchmarking) which compares the most used PHP serializers. Take a look at the results and you'll realize that performance still need to be boosted by the other vendors. I would like to say thanks to [Eduard Sukharev](https://github.com/eduard-sukharev) which did (and keep doing) great contributions on this project.

## Instalation

You can install this library through composer:

`composer require tsantos/serializer`

or just add `tsantos/serializer` to your composer file and then

`composer update`

Flex recipe: comming soon!

## Usage

The best way to get start with `TSantos Serializer` is by using the builder. With a few configurations you are ready to serialize your data:

```php
$builder = new SerializerBuilder();

$serializer = $builder->build();

$person = new Person(100, 'Tales Santos');

echo $serializer->serialize($person); // {"id":100, "name":"Tales Santos"}
```

Unlike other libraries, all properties should either be `public` or have a public `getter` and `setter` methods.

```php
# path/to/my/entities
namespace My\Namespace\Entity

class Person
{
    /** @var string */
    public $name;
    public $age;
    private $colors;

    public function setName(string $name) { $this->name = $name; }

    /** @return integer */
    public function getAge(): int { return $this->age; }
    public function setAge(int $age) { $this->age = $age; }

    /** @return array */
    public function getColors(): array { return $this->colors; }
    public function setColors(array $colors) { $this->colors = $colors; }
}
```

## Features

Features currently supported by TSantos Serializer:

* Supports `PHP`, `YAML`, `XML`, `Reflection`, `Annotations` and `InMemory (array)` mapping formats
* Supports `JSON` encoders (output)
* (De-)serializes objects of any depth
* Custom `getters` and `setters`
* Virtual properties
* Properties grouping

## Call for contribution

This project is still in development and are fully open to your contribution. Want to contribute? Choose your feature bellow and lets discuss about it:

* Support for `XML` and `CSV` encoders (output)
* Documentation
* Any idea?
