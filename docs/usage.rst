Usage
=====

All the (de-)serialization processes needs an instance of `SerializerInterface` which can be easily created through the
`SerializerBuilder`. All the examples in this page will consider that the serializer instance is already created in a
way like this::

    $serializer = (new SerializerBuilder())
        ->build();

Serialize Objects
-----------------

The simplest way to serialize an object is pass a data to `SerializerInterface::serialize` method::

    $post = new App\Entity\Post(100, 'Post Title');
    $encoded = $serializer->serialize($post);
    echo $encoded; // {"id":100, "name":"Post Title"}

Collections
~~~~~~~~~~~

You can serialize a collection of objects in the same way you serialize a single object::

    $comments = ... ;
    $encoded = $serializer->serialize($comments);
    echo $encoded; // [{"content":"Comment 1"}, {"content":"Comment 2"}]

Handling Circular Reference
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Circular references occurs when an object A serializes an object B which in turns serializes the A again. This library
is able to detect such situation and throws the `CircularReferenceException` exception. In addition you can control
how many times the same object will be serialized before the exception is thrown::

    try {
        $post = ... ;
        $context = (new SerializationContext())->setCircularReferenceCount(3);
        $encoded = $serializer->serialize($post, $context);
        echo $encoded; // {"id":100, "name":"Post Title"}
    } catch (\TSantos\Serializer\Exception\CircularReferenceException $e) {
    }

Deserialize Objects
-------------------

The inverse operation (e.g: deserialization) is quite simple as serializing objects. You just need to provide the type
and format of the data being deserialized::

    $json = '{"id":100, "name":"Post Title"}';
    $post = $serializer->deserialize($json, Post::class, 'json');
    echo get_class($post); // App\Entity\Post

Object Instantiator
~~~~~~~~~~~~~~~~~~~

The serializer will instantiate a new class based on the `type` parameter of the method `SerializerInterface::deserialize.`
By default it uses the Doctrine Object instantiator to create new instances but you can define your own implementation
and configure the serializer to use it::

    use TSantos\Serializer\ObjectInstantiator\ObjectInstantiatorInterface;

    class MyObjectInstantiator implements ObjectInstantiatorInterface
    {
        public function create(string $type, array $data, DeserializationContext $context)
        {
            return new $type();
        }
    }

and then::

    $serializer = (new SerializerBuilder())
        ->setObjectInstantiator(new MyObjectInstantiator())
        ->build();

Targeting the Deserialization
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The serializer can populate the data into an existing object instead of instantiate a fresh instance::

    $json = '{"name":"Post Title"}';
    $post = ...;
    $context = (new DeserializationContext())->setTarget($post);
    $post = $serializer->deserialize($json, Post::class, 'json', $post);

Normalizers
-----------

Normalizers are powerful services that handles a specific data type and returns its handled version.

Built-in Normalizers
~~~~~~~~~~~~~~~~~~~~

ObjectNormalizer:
    Is the most important normalizer in this library. It can receive the object being serialized and normalize it to
    array.

CollectionNormalizer:
    This normalizer iterates over a collection of objects and serializes each of them.

JsonNormalizer:
    This normalizer checks whether the object being serialized implements the `JsonSerializable` interface and call
    the method `jsonSerialize` to normalized the data.

Custom normalizers can be easily added to serializer::

    class AuthorNormalizer implements NormalizerInterface
    {
        public function normalize($data, SerializationContext $context)
        {
            return $data->getUsername();
        }

        public function supportsNormalization($data, SerializationContext $context): bool
        {
            return $data instanceof Author;
        }
    }

and then::

    $builder = (new SerializerBuilder())
        ->addNormalizer(new AuthorNormalizer())
        ->build();

Encoders
--------

Encoders are services that encodes a normalized data into a specific format and vice-versa.

Built-in Encoders
~~~~~~~~~~~~~~~~~

JsonEncoder:
    Encodes and decode data in JSON format.

Event Listeners
---------------

Event listeners gives you the ability to hook into a serialization process. They ables you the opportunity to change
the data before and after a serialization process.::

    $builder = (new SerializerBuilder())
        ->addListener(Events::PRE_SERIALIZATION, function (PreSerializationEvent $event) {
            /** @var Post $post */
            $post = $event->getObject();
            $post->setSummary('modified summary');
        })
        ->build();

Event Subscribers
~~~~~~~~~~~~~~~~~

Instead of adding listener through closures, you can add event subscribers to add listeners to serializer::

    class MyEventSubscriber implements EventSubscriberInterface
    {
        public static function getListeners(): array
        {
            return [
                Events::PRE_SERIALIZATION => 'onPreSerialization',
                Events::POST_SERIALIZATION => 'onPostSerialization',
                Events::PRE_DESERIALIZATION => 'onPreDeserialization',
                Events::POST_DESERIALIZATION => 'onPostDeserialization',
            ];
        }

        public function onPreSerialization(PreSerializationEvent $event): void {}
        public function onPostSerialization(PostSerializationEvent $event): void {}
        public function onPreDeserialization(PreDeserializationEvent $event): void {}
        public function onPostDeserialization(PostDeserializationEvent $event): void {}
    }

and then::

    $builder = (new SerializerBuilder())
        ->addSubscriber(new MyEventSubscriber())
        ->build();

Events
~~~~~~

Events::PRE_SERIALIZATION:
    Listeners have the opportunity to change the state of the object before the serialization.

Events::POST_SERIALIZATION::
    Listeners have the opportunity to change the array generated by de serialization.

Events::PRE_DESERIALIZATION::
    Listeners have the opportunity to change the array generated by the deserialization.

Events::POST_DESERIALIZATION::
    Listeners have the opportunity to do some validations after the deserialization finishes.

Caching
-------

The serialize can cache two types of information: a) the generated serializer classes and b) the class metadata.

Class Cache
~~~~~~~~~~~

You should provide the location where the generated serializer classes will be stored. Defaults to
`/tmp/serializer/classes`::

    $serializer = (new SerializerBuilder())
        ->setClassCacheDir(__DIR__ . '/var/cache/serializer/classes')
        ->build();

Metadata Cache
~~~~~~~~~~~~~~

To avoid parsing all classes to read its metadata data all the time, the serializer can cache the metadata and use it on
the subsequent request::

    $serializer = (new SerializerBuilder())
        ->setMetadataCacheDir(__DIR__ . '/var/cache/serializer/metadata')
        ->build();

Built-in metadata cache strategies:

FileCache:
    Will be automatically configured when provide a directory like the bellow example.

DoctrineCacheAdapter:
    Any class implementing `Cache` interface of Doctrine

    .. code-block:: php-annotations

        $serializer = (new SerializerBuilder())
            ->setMetadataCache(new DoctrineCacheAdapter(
                new \Doctrine\Common\Cache\RedisCache(...)
            ))
            ->build();

PsrCacheAdapter:
    Any class implementing `CacheItemPoolInterface` interface.

    .. code-block:: php-annotations

        $serializer = (new SerializerBuilder())
            ->setMetadataCache(new PsrCacheAdapter(
                $psrCache
            ))
            ->build();

Class Generation
----------------

This library generates PHP classes that will convert the objects to array and vice-versa. Those classes are automatically
generated based on you class mapping and stored in somewhere defined in your project. Therefore, to avoid unnecessary
I/O to generate those classes, you can configure the strategy when generating them.

FileNotExists:
    This strategy will generate the classes only if they don't exist in filesystem. Good for development environments

    .. code-block:: php-annotations

        $serializer = (new SerializerBuilder())
            ->setSerializerClassGenerateStrategy(SerializerClassLoader::AUTOGENERATE_FILE_NOT_EXISTS)
            ->build();

Always:
    The classes will be generated regardless the existence of the classes. Good for debugging

    .. code-block:: php-annotations

        $serializer = (new SerializerBuilder())
            ->setSerializerClassGenerateStrategy(SerializerClassLoader::AUTOGENERATE_ALWAYS)
            ->build();

Never:
    The serializer will never check the classes' existence and never generate them. This strategy can improve the
    performance in production environment

    .. code-block:: php-annotations

        $serializer = (new SerializerBuilder())
            ->setSerializerClassGenerateStrategy(SerializerClassLoader::AUTOGENERATE_NEVER)
            ->build();
