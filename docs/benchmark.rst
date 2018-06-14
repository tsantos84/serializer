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
