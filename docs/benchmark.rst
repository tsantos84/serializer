Benchmark
=========

There are a lot of other libraries that does the same job as TSantos Serializer like `JMS Serializer` and
`Symfony Serializer`. However, after making some benchmark in those library, I realized that they have a considerable
overhead when serializing some data.

That's why I decided to write a different technique to serialize complex objects and the results were very satisfactory:

.. image:: /img/serialization_bench.png

As you can see, TSantos Serializer is 6x and 7x faster than Symfony Serializer and JMS Serializer respectively. This
benchmark was generated through the `PHPBench <http://phpbench.readthedocs.io/en/latest/>`_.

The source code of this benchmark is available on `GitHub <https://github.com/tsantos84/serializer-benchmark>`_ so you
can clone and run it by yourself.

Performance Notes
-----------------

The serialization process can be separated in two main operations: `compile time` and `runtime`.

Compile time:
    Operation that compile the class metadata and persist them in some cache implementation. This operation is very slow
    because it should make some I/O in filesystem. This operation should be always avoided in production environments.
    A good practice is generate the class metadata before deploying your application to production.

Runtime:
    Operation that transforms the data. Very fast after the class metadata is already compiled.

Another important topic about performance is how the serializer will read and write data from your objects. By using
explicit accessors and mutators is slightly faster then using reflection to access private/protected properties. Keep
this in mind in order to boost your application.
