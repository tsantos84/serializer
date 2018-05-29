Mapping
=======

Annotation
----------

.. code-block:: php-annotations

    namespace App\Entity;

    use TSantos\Serializer\Mapping as Serializer;

    /**
     * @Serializer\BaseClass("App\Serializer\BaseClass")
     */
    class Person
    {
        /**
         * @Serializer\Type("integer")
         */
        private $id;

        /**
         * @Serializer\Type("string")
         * @Serializer\Groups({"api"})
         */
        private $name;

        /**
         * @Serializer\Type("string")
         */
        private $lastName;

        /**
         * @Serializer\Getter("isMarried")
         * @Serializer\ExposeAs("is_married")
         */
        private $married;

        /**
         * @Serializer\Type("DateTime")
         * @Serializer\Modifier("format('d/m/Y')")
         */
        private $birthday;

        /**
         * @Serializer\ReadOnly
         */
        private $address;

        /**
         * @Serializer\Type(Person::class)
         */
        private $father;

        // ... getters and setters

        /**
         * @Serializer\VirtualProperty
         * @Serializer\Type("string")
         * @Serializer\ExposeAs("full_name")
         * @Serializer\Groups({"api"})
         */
        public function getFullName(): string
        {
            return trim($this->name . ' ' . $this->lastName);
        }

        /**
         * @Serializer\VirtualProperty()
         */
        public function getFormattedAddress(): string
        {
            if (null === $this->address) {
                return '';
            }

            return $this->address->getStreet() . ' ' . $this->address->getCity();
        }
    }

.. note::

    The annotation metadata driver is not ready to use by default. You need to require the package `doctrine/annotations`
    to your project and then enable the driver through the `SerializerBuilder`::

        $builder = new SerializerBuilder();

        $serializer = $builder
          ->enableAnnotations()
          ->build();

YAML
----

.. code-block:: yaml

    App\Entity:
        baseClass: App\Serializer\BaseClass
            properties:
                id:
                    type: integer
                name:
                    type: string
                    groups: ["api"]
                lastName:
                    type: string
                married:
                    type: boolean
                    getter: isMarried
                    exposeAs: is_married
                birthday:
                    type: DateTime
                    modifier: "format('d/m/Y')"
                father:
                    type: App\Entity\Person
                address:
                    type: App\Entity\Address
                    readOnly: true
            virtualProperties:
                getFullName:
                    type: string
                    exposeAs: full_name
                    groups: ["api"]
                getFormattedAddress:
                    type: string

.. note::

    The yaml metadata driver is not ready to use by default. You must to require the package `symfony/yaml`
    to your project.

XML
---

.. code-block:: xml

    <?xml version="1.0" encoding="utf-8" ?>
    <serializer>
        <class name="App\Entity\Person" base-class="App\Serializer\BaseClass">
            <property name="id" type="integer" />
            <property name="name" type="string" expose-as="nome" groups="api" />
            <property name="lastName" type="string" />
            <property name="married" type="boolean" getter="isMarried" expose-as="is_married" />
            <property name="birthday" type="DateTime" modifier="format('d/m/Y')" />
            <property name="address" read-only="true" />
            <property name="father" type="App\Entity\Person" />
            <virtual_property name="getFullName" type="string" expose-as="full_name" >
                <groups>
                    <value>api</value>
                </groups>
            </virtual_property>
            <virtual_property name="getFormattedAddress" />
        </class>
    </serializer>
