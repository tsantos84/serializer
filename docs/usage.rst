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

Targeting the Deserialization
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The deserialization process can populate the data into an existing object::

    $json = '{"name":"Post Title"}';
    $post = ...;
    $context = (new DeserializationContext())->setTarget($post);
    $post = $serializer->deserialize($json, Post::class, 'json', $post);

Event Listeners
---------------

