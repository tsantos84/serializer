Custom Metadata Configurator
============================

A useful way to control how to expose your data is through metadata configurators. They can change, for example, round
all numeric properties before encoding the data. To do so, you need to create a custom `TSantos\\Serializer\\Metadata\\ConfiguratorInterface`::

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

                $property->readValueFilter = sprintf('round($value, %d, %d)', $precision, $mode);
            }
        }
    }

Done! The configurator is able to round the values of all configured properties. The last step is tell the serializer
which properties should be rounded::

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

When you deserialize the `Order` class, the amount will be rounded::

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
    This configuration will change the behavior of all classes.
