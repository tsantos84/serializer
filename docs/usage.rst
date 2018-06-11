Usage
=====

The Serializer instance
-----------------------

Creating a new instance of `TSantos\\Serializer\\SerializerInterface` is a complex work because it has a couple of
dependencies and instantiate all of them manually can be hard. Thanks to `TSantos\\Serializer\\SerializerBuilder`
you can quickly create an instance of `SerializerInterface` and start transforming your data without much effort::

    $builder = (new SerializerBuilder())->build();
    $post = new App\Entity\Post(100, 'Post Title');
    echo $serializer->serialize($post); // {"id":100, "name":"Post Title"}
