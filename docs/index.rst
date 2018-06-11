TSantos Serializer Library
==========================
|build| |scrutinizer| |code_coverage| |docs|

.. |build| image:: https://travis-ci.org/tsantos84/serializer.svg?branch=master
.. |docs| image:: https://readthedocs.org/projects/tsantos-serializer/badge/?version=latest
.. |scrutinizer| image:: https://scrutinizer-ci.com/g/tsantos84/serializer/badges/quality-score.png?b=master
.. |code_coverage| image:: https://scrutinizer-ci.com/g/tsantos84/serializer/badges/coverage.png?b=master

Welcome to TSantos Serializer documentation page! This page will show you how to install and use the TSantos Serializer
library.

Introduction
------------

With the growth of micro-services a good serializer tool should be use to expose your data over the internet.
Such tools should be able to read data (commonly PHP objects) and encode it to any format to be consumed by API client.
TSantos Serializer was built focused on performance and APIs which requires fast responses, low CPU and low
memory usage as non-functional requirements. Moreover, this library tries to do all this process without loosing the
benefits of data-mapping and flexible configurations.

Summary
-------

.. toctree::
   :maxdepth: 1

   installation
   usage
   mapping
   accessors
   how_it_works
