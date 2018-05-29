How It Works
============

The goal of this library is to encode/decode your objects in any format as fast as possible. To do so, the serializer
automatically generates classes that can read/write data from/to your objects with the minimum overhead. Bellow
you can see a resumed content of `Post` class accessed through its `accessors`::

    <?php

    final class AppDocumentPostSerializer extends TSantos\Serializer\AbstractSerializerClass
    {
        public function serialize($object, SerializationContext $context): array
        {
            $data = [];
            $exposedKeys = ...;
            $shouldSerializeNull = $context->shouldSerializeNull();

            if (isset($exposedKeys['id'])) {
                if (null !== $value = $object->getId()) {
                    $data['id'] = (integer) $value;
                } elseif ($shouldSerializeNull) {
                    $data['id'] = null;
                }
            }

            if (isset($exposedKeys['title'])) {
                if (null !== $value = $object->getTitle()) {
                    $data['title'] = (string) $value;
                } elseif ($shouldSerializeNull) {
                    $data['title'] = null;
                }
            }

            if (isset($exposedKeys['slug'])) {
                if (null !== $value = $object->getSlug()) {
                    $data['slug'] = (string) $value;
                } elseif ($shouldSerializeNull) {
                    $data['slug'] = null;
                }
            }

            if (isset($exposedKeys['summary'])) {
                if (null !== $value = $object->getSummary()) {
                    $data['summary'] = (string) $value;
                } elseif ($shouldSerializeNull) {
                    $data['summary'] = null;
                }
            }

            if (isset($exposedKeys['content'])) {
                if (null !== $value = $object->getContent()) {
                    $data['content'] = (string) $value;
                } elseif ($shouldSerializeNull) {
                    $data['content'] = null;
                }
            }

            return $data;
        }

        public function deserialize($object, array $data, DeserializationContext $context)
        {
            $exposedKeys = ...;

            if (isset($data['id']) && isset($exposedKeys['id'])) {
                $object->setId($id);
            }

            if (isset($data['title']) && isset($exposedKeys['title'])) {
                $object->setTitle($data['title']);
            }

            if (isset($data['summary']) && isset($exposedKeys['summary'])) {
                $object->setSummary($data['summary']);
            }

            if (isset($data['slug']) && isset($exposedKeys['slug'])) {
                $object->setSlug($data['slug']);
            }

            if (isset($data['content']) && isset($exposedKeys['content'])) {
                $object->setContent($data['content']);
            }

            return $object;
        }
    }

In fact this class has two responsibilities: 1) given a filled object, return its array version (e.g: serialize method);
2) given an empty object and a data to be populated, fill in the object with that data (e.g: deserialize method).

.. note::

    There are two ways to read/write data from/to your objects. Please, refer to the :doc:`dedicated page </accessors>`
    to find out which of those strategies is better for your application.

Whenever you make the call `$serializer->serialize($post)` the serializer will locate its generated class, call its
`serialize` method and get its array representation. After that, the result of that call will be encoded through the
`encoder` configured by the `SerializerBuilder`.
