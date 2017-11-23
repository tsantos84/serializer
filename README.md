# TSantos Serializer [![Build Status](https://travis-ci.org/tsantos84/serializer.svg?branch=master)](https://travis-ci.org/tsantos84/serializer)

## Introduction

With the growth of micro-services a good serializer tool should be use to expose your data over the internet. Such tools should be able to read data (commonly PHP objects) and encode it to any format to be returned for some API client. TSantos Serializer was built focused on performance and APIs which requires fast responses, low CPU and low memory usage as non-functional requirements. Moreover, this library tries to do all this proccess without loosing the benefits of data-mapping and flexible configurations.

## Motivation

No, I'm not forgetting the DRY concept and I have good reasons to writing this library. I know that there are most known library that do this job like JMS Serializer and Symfony Serializer, but I'm not satisfied with its performance. They have a considerable overhead and in almost time they consume precious miliseconds of RESTful APIs.

I used to use JMS a lot and I really like that tool, but now I want to get my application's performance to a high level without loosing the great feature as that library provides.

Still not convinced? I've created a benchmark application which compares the most used PHP serializers. Take a look at the results and you'll realize that performance still need to be boosted by the other vendors. I would like to say thanks to [Eduard Sukharev](https://github.com/eduard-sukharev) which did (and keep doing) great contributions on this project.

## Instalation

You can install this library through composer:

`composer require tsantos/serializer`

or just add `tsantos/serializer` to your composer file and then

`composer update`

Flex recipe: comming soon!

## Usage

The best way to get start with `Tsantos Serializer` is by using the builder. With a few configurations you are ready to serialize your data:

```php
$builder = new SerializerBuilder();

$serializer = $builder
  ->addMetadataDir('My\Namespace\Entity', 'path/to/my/mapping')
  ->build();

$person = new Person(100, 'Tales Santos');

echo $serializer->serialize($person, 'json'); // {"id":100, "name":"Tales Santos"}
```

All objects should have a mapping file describing the properties and its configuration.

```yaml
# path/to/my/mapping/Person.yaml
My\Namespace\Entity\Person:
  properties:
    id:
      type: integer
    name:
      type: string
```

Unlike other libraries, only those properties described in the mapping file will be serialized and all properties should have a public `getter` method. 

```php
# path/to/my/entities
namespace My\Namespace\Entity

class Person
{
  private $id;
  private $name;
  
  public function __construct(int $id, string $name)
  {
    $this->id = $id;
    $this->name = $name;
  }
  
  public function getId() { return $this->id; }
  public function getName() { return $this->name; }
}
```

## Features

Current features supported by TSantos Serializer:

* Supports `PHP`, `YAML`, `XML` and `InMemory (array)` mapping formats
* Supports `JSON` encoders (output)
* Serializes objects of any depth
* Custom `getters`
* Virtual properties
* Properties grouping

## Call for contribution

This project is still in development and are fully open to your contribution. Want to contribute? Choose your feature bellow and lets discuss about it:

* Ability to deserialize objects
* Support for `XML` and `CSV` encoders (output)
* Support for ready configuration from annotations
* Documentation
* Any idea?
