Custom Metadata Configurator
============================

A useful way to control how the hydrators should be generated is through metadata configurators.
They can change, for example, how to read/write data from/to your objects. Suppose you want to round the order
amount before encoding the data. To do so, you need to create a custom `TSantos\\Serializer\\Metadata\\ConfiguratorInterface`::

    namespace App\Serializer\Metadata\Configurator;

    use TSantos\Serializer\Metadata\ConfiguratorInterface;
    use TSantos\Serializer\Metadata\ClassMetadata;

    class PropertyRoundConfigurator implements ConfiguratorInterface
    {
        public function configure(ClassMetadata $classMetadata): void
        {
            foreach ($classMetadata->properties as $property) {

                // another metadata configurator can also change this option, so
                // we make this check to avoid some side effect
                if (null !== $property->readValueFilter) {
                    continue;
                }

                // we want to change only float properties
                if ('float' !== $property->type)) {
                    continue;
                }

                // we want to round only explicitly properties
                if (!isset($property->options['round'])) {
                    continue;
                }

                $precision = (int) $property->options['round'];
                $mode = $property->options['mode'] ?? PHP_ROUND_HALF_UP;

                $property->readValueFilter = \sprintf('\round($value, %d, %d)', $precision, $mode);
            }
        }
    }

The next step is register your metadata configurator into the serializer::

    $serializer = (new SerializerBuilder())
        ->addMetadataConfigurator(new PropertyRoundConfigurator())
        ->build();

Done! The configurator is able to round the values of all configured properties and the serializer is aware of it.
The next and last step is to tell the serializer which properties should be rounded::

    namespace App\Entity;

    class Order
    {
        /**
         * @Serializer\Options({
         *  "round": 2,
         *  "mode": 1
         * })
         */
        private $amount;

        public function setAmount(float $amount): void
        {
            $this->amount = $amount;
        }
    }

When you serialize the `Order` class, the amount will be rounded exactly as you configured::

    class OrderController
    {
        public function getOrderAction()
        {
            $order = new Order();
            $order->setAmount(100.599);

            $json = $serializer->serialize($order);

            echo($json); // {"amount":100.6}
        }
    }

.. note::
    This is useful when you want to make single changes in the value. For more complex filters, use `EventListeners` to
    hook into serialize operations and change the array resulted from serialization.
