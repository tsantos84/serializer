Data Extractors
===============

There are two ways to read and write data from your objects: through `accessors` or `reflection`. Each instance of
`SerializerInterface` should be configured with one of those data extractor.

Accessors
---------

This extractor will make use of `getters` and `setters` of your class to read and write data on your objects. The
`SerializerBuilder` is configured to use this accessor by default::

    $builder = new SerializerBuilder();

    $serializer = $builder
      ->accessThroughAccessors()
      ->build();

By default the metadata mechanism will look for methods starting with `get` and `set` to access the mapped properties.
You can change this behavior by defining custom accessors to your properties:

.. code-block:: php-annotations

    namespace App\Entity;

    use TSantos\Serializer\Mapping as Serializer;

    class Post
    {
        /**
         * @Serializer\Getter("getIdentification")
         * @Serializer\Setter("setIdentification")
         */
        private $id;

        public function getIdentification(): integer
        {
            return $this->id;
        }

        public function setIdentification(integer $id): void
        {
            $this->id = $id;
        }
    }

.. code-block:: yaml

    App\Entity\Post:
        properties:
            id:
                getter: "getIdentification"
                setter: "setIdentification"

.. code-block:: xml

    <?xml version="1.0" encoding="utf-8" ?>
    <serializer>
        <class name="App\Entity\Post">
            <property name="id" getter="getIdentification" setter="setIdentification"/>
        </class>
    </serializer>

.. note::

    Extracting data through `accessors` is faster than `reflection` and should be used whenever possible.

Reflection
----------

This extractor uses reflection to read and write data and ables you to write thin classes without any `getter` or
`setter`, it means that you can map classes containing only private members and the serializer still have the ability to
read and write data in your objects::

    namespace App\Entity;

    use TSantos\Serializer\Mapping as Serializer;

    class Post
    {
        /**
         * @Serializer\Type("integer")
         */
        private $id;
    }

You need to enable this extractor in your builder to use this extractor strategy::

    $builder = new SerializerBuilder();

    $serializer = $builder
      ->accessThroughReflection()
      ->build();

.. note::

    This extractor is indicated when you have value objects which don't have any `setter` methods.
