Mapping
=======

Telling to serializer how it should transform your data is a very important step to assert that the transformation will
not serialize the data with a wrong data type.

Zero User Mapping
-----------------

TSantos Serializer will do its better to extract the must important metadata information from your class by reading
its structure without the needing add custom annotations on every single property of your class. All you need to do is
to write classes with good type-hint parameters and proper return types::

    namespace App\Entity;

    class Post
    {
        private $id;
        private $title;
        private $comments;
        private $author;
        public function __construct(int $id) { ... } // mutator
        public function setTitle(string $title): { ... } // mutator
        public function addComment(Comment $comment) { ... } // mutator
        public function getAuthor(): Author { ... } // accessor
    }

The serializer is smart enough to extract the data types from the property's mutators and accessors and will
(de-)serialize them respecting those types.

The previous example is a good start point because you don't need to worry about the boring process of mapping all
properties of all classes you want to serialize and is enough to cover the must use cases. However, you don't have any
control of what data should be serialized and when it should be serialized. That's why you should use the mapping
mechanism if you want a refined control over the serialization process.

The Serializer Builder
----------------------

Before going ahead with mapping options, lets see how you should use the Serializer Builder to tell the serialize
where are your mapping information::

    $builder = new SerializerBuilder();

    $serializer = $builder
      ->addMetadataDir('App\Document', '/project/config/serializer') // yaml metadata files
      ->addMetadataDir('App\Entity', '/project/src/Entity') // annotation metadata files
      ->build();

.. note::
    Because the builder accepts many metadata directories, you can mix the supported mapping formats in the same
    serializer instance.

.. note::
    You need to require the Symfony Yaml component to map your classes using the YAML format

    .. code-block:: bash

        $ composer require symfony/yaml


.. note::
    You need to require the Doctrine Annotations component to map your classes using annotations syntax

    .. code-block:: bash

        $ composer require doctrine/annotations

    and then enable the annotation reader in the `Serializer Builder`::

        $serializer = $builder
          ->enableAnnotations()
          ->build();


Options Reference
-----------------

BaseClass
~~~~~~~~~

Define what class the generated hydrator class should extends

.. code-block:: php-annotations

    /**
     * @BaseClass("My\Custom\Class")
     */
    class Post {}

.. code-block:: yaml

    App\Entity\Post:
        baseClass: "My\Custom\Class"

.. code-block:: xml

    <class name="App\Entity\Post" base-class="My\Custom\Class">

Discriminator
~~~~~~~~~~~~~

Discriminates the sub-types of an abstract class.

.. code-block:: php-annotations

    /**
     * @Discriminator(field="type", map={"car":"App\Entity\Car","airplane":"App\Entity\Airplane"})
     */
    abstract class AbstractVehicle {}
    class Car extends AbstractVehicle {}
    class Airplane extends AbstractVehicle {}

.. code-block:: yaml

    App\Entity\AbstractVehicle:
        discriminatorField: "type"
        discriminatorMap:
            car: "App\\Entity\\Car"
            airplane: "App\\Entity\\airplane"

.. code-block:: xml

    <class name="App\Entity\AbstractVehicle">
        <discriminator field="type">
            <map value="car">App\Entity\Car</map>
            <map value="airplane">App\Entity\Airplane</map>
        </discriminator>
    </class>

Hydrator Construct Args
~~~~~~~~~~~~~~~~~~~~~~~

Provides the set of arguments that should be passed to hydrators when constructing them.

.. code-block:: php-annotations

    /**
     * @HydratorConstructArgs(args={"users":"@App\Repository\UserRepository", "foo":"bar"})
     */
    class Order {}

.. code-block:: yaml

    App\Entity\Order:
        hydratorConstructArgs:
            users: "@App\\Repository\\UserRepository"
            foo: "bar"

.. code-block:: xml

    <class name="App\Entity\Order">
        <hydrator_construct_args>
            <arg name="car">@App\Entity\Car</map>
            <arg name="foo">bar</map>
        </hydrator_construct_args>
    </class>

.. note::
    By prefixing the argument value with "@", the value will be treated as a service name and the correspondent service
    will be passed to hydrators as dependency.

ExposeAs
~~~~~~~~

The serialized name

.. code-block:: php-annotations

    /**
     * @ExposeAs("full_name")
     */
    private $fullName;

.. code-block:: yaml

    properties:
        fullName:
            exposeAs: "full_name"

.. code-block:: xml

    <property name="fullName" type="integer" expose-as="full_name" />

Getter
~~~~~~

The accessor method to read the value

.. code-block:: php-annotations

    /**
     * @Getter("getMyCustomFullName")
     */
    private $fullName;

.. code-block:: yaml

    properties:
        fullName:
            getter: "getMyCustomFullName"

.. code-block:: xml

    <property name="fullName" getter="getMyCustomFullName" />

.. tip::

    If you omit the `getter` option, the serializer will try to guess the getter automatically

Groups
~~~~~~

The list of groups that the property can be serialized

.. code-block:: php-annotations

    /**
     * @Groups({"web","v1"})
     */
    private $fullName;

.. code-block:: yaml

    properties:
        fullName:
            groups: ["web", "v1"]

.. code-block:: xml

    <property name="fullName" groups="web,v1" />
    <!-- or -->
    <property name="fullName">
        <groups>
            <value>web</value>
            <value>v1</value>
        </groups>
    </property>

Options
~~~~~~~

A key/value used by metadata configurators

.. code-block:: php-annotations

    /**
     * @Options({"format":"Y-m-d"})
     */
    private $birthday;

.. code-block:: yaml

    properties:
        birthday:
            options: {"format":"Y-m-d"}

.. code-block:: xml

    <property name="birthday">
        <options>
            <option name="format">Y-m-d</option>
        </options>
    </property>

.. tip::

    Metadata configurators can access the property's options to modify its behavior.

Read Only
~~~~~~~~~

The property cannot be deserialized

.. code-block:: php-annotations

    /**
     * @ReadOnly
     */
    private $id;

.. code-block:: yaml

    properties:
        id:
            readOnly: true

.. code-block:: xml

    <property name="id" read-only="true">

Read Value Filter
~~~~~~~~~~~~~~~~~

A filter applied to the property value before encoding

.. code-block:: php-annotations

    /**
     * @ReadValueFilter("strtolower($value)")
     */
    private $username;

.. code-block:: yaml

    properties:
        username:
            readValueFilter: "strtolower($value)"

.. code-block:: xml

    <property name="username" read-value-filter="strtolower($value)" />

.. tip::

    Metadata configurators can change the `read-value-filter` to customize the input/output of property's values.

Setter
~~~~~~

The mutator method to write the value

.. code-block:: php-annotations

    /**
     * @Setter("setMyCustomFullName")
     */
    private $fullName;

.. code-block:: yaml

    properties:
        fullName:
            getter: "setMyCustomFullName"

.. code-block:: xml

    <property name="fullName" getter="setMyCustomFullName" />

.. tip::

    If you omit the `setter` option, the serializer will try to guess the setter automatically.

Type
~~~~

The data type of mapped property

.. code-block:: php-annotations

    /**
     * @Type("integer")
     */
    private $id;

.. code-block:: yaml

    properties:
        id:
            type: "integer"

.. code-block:: xml

    <property name="id" type="integer" />

.. tip::

    If you omit the type, the serializer will try to guess the type automatically.

Virtual Property
~~~~~~~~~~~~~~~~

Mark a method as a virtual property. Its return will be encoded within the properties data.

.. code-block:: php-annotations

    /**
     * @VirtualProperty
     */
    public function getAge(): int
    {
        ...
    }

.. code-block:: yaml

    virtualProperties:
        getAge: ~

.. code-block:: xml

    <virtual-property name="getAge" />

.. tip::

    If you omit the type option, the serializer will try to guess the type automatically thanks to metadata configurators.

Write Value Filter
~~~~~~~~~~~~~~~~~~

A filter applied to the property value before writing it to objects

.. code-block:: php-annotations

    /**
     * @WriteValueFilter("\DateTime::createFromFormat('Y-m-d', $value)")
     */
    private $birthday;

.. code-block:: yaml

    properties:
        birthday:
            writeValueFilter: "\DateTime::createFromFormat('Y-m-d', $value)"

.. code-block:: xml

    <property name="username" write-value-filter="\DateTime::createFromFormat('Y-m-d', $value)" />

.. tip::

    Metadata configurators can change the `write-value-filter` to customize the input/output of property's values.

Performance
-----------

There is no difference in terms of performance between the mapping formats. In fact, the metadata generated by the
mapping will be cached and reused in the next serialization operation, so you can choose the most comfortable format
for you.
