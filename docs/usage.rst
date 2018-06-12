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

Handling Circular Reference
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Circular references occurs when an object A serializes an object B which in turns serializes the A again. This library
is able to detect this situation and throws the `CircularReferenceException` exception. In addition you can control
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

The inverse operation (e.g: deserialization) is quite simple as serializing objects. You just need to provide the format
of the data being deserialized::

    $json = '{"id":100, "name":"Post Title"}';
    $post = $serializer->deserialize($json, 'json');
    echo get_class($post); // App\Entity\Post

Event Listeners
---------------

