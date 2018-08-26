Inject Services Into Hydrators
==============================

Sometimes you want to inject custom services into your hydrators to use them when extract or hydrate your data. A real
use case is when you want to access, for example, your `UserRepository` to hydrate back an object which has the `App\\Entity\\User`
as a dependency::

    namespace App\Entity;

    class User
    {
        private $id;

        public function getId(): int
        {
            return $this->id;
        }
    }

    /**
     * @Serializer\HydratorConstructArgs(
     *  "users": {
     *       "type": "App\Repository\UserRepository",
     *       "value":"@App\Repository\UserRepository"
     *   }
     * )
     */
    class Order
    {
        /**
         * @var User
         * @Serializer\ReadValueFilter("$value->getId()")
         * @Serializer\WriteValueFilter("$this->users->find($value)")
         */
        private $user;

        /** @var array */
        private $items;

        public function getUser(): User
        {
            return $this->user;
        }
    }

As you want to inject a custom service into your hydrator, you should create the `SerializerBuilder` with a custom instance
of Pimple `Container`::

    use App\Repository\UserRepository;
    use Pimple\Container;
    use TSantos\Serializer\SerializerBuilder;

    $myContainer = new Container([
        UserRepository::class => function () {
            return new UserRepository();
        }
    ]);

    $builder = new SerializerBuilder($myContainer);

    $serializer = $builder->build();

Instead of hydrate the id attribute with plain value (e.g: int) into the order instance, the hydrator will fetch the user
from your repository and will hydrate the `user` attribute with it::

    class OrderController
    {
        public function saveAction()
        {
            $json = '{"items":[],"user":1}';

            $order = $serializer->deserialize($json, Order::class);

            var_dump($order->getUser());
        }
    }

.. note::
    You can achieve the same functionality with custom `denormalizers` but changing the hydrator behavior will not add
    any overhead to your application, just the costs made by the `UserRepository::find()` method.
