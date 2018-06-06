<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Instantiator\Instantiator;
use Metadata\Cache\CacheInterface;
use Metadata\Cache\FileCache;
use Metadata\Driver\DriverChain;
use Metadata\Driver\DriverInterface;
use Metadata\Driver\FileLocator;
use Metadata\MetadataFactory;
use TSantos\Serializer\Encoder\EncoderInterface;
use TSantos\Serializer\Encoder\JsonEncoder;
use TSantos\Serializer\EventDispatcher\EventDispatcher;
use TSantos\Serializer\EventDispatcher\EventSubscriberInterface;
use TSantos\Serializer\Metadata\Configurator\DateTimeConfigurator;
use TSantos\Serializer\Metadata\Configurator\GetterConfigurator;
use TSantos\Serializer\Metadata\Configurator\TypeConfigurator;
use TSantos\Serializer\Metadata\Configurator\SetterConfigurator;
use TSantos\Serializer\Metadata\Driver\AnnotationDriver;
use TSantos\Serializer\Metadata\Driver\ConfiguratorDriver;
use TSantos\Serializer\Metadata\Driver\XmlDriver;
use TSantos\Serializer\Metadata\Driver\YamlDriver;
use TSantos\Serializer\Normalizer\CollectionNormalizer;
use TSantos\Serializer\Normalizer\DateTimeNormalizer;
use TSantos\Serializer\Normalizer\JsonNormalizer;
use TSantos\Serializer\Normalizer\ObjectNormalizer;
use TSantos\Serializer\Normalizer\ScalarNormalizer;
use TSantos\Serializer\ObjectInstantiator\DoctrineInstantiator;
use TSantos\Serializer\ObjectInstantiator\ObjectInstantiatorInterface;

/**
 * Class Builder
 *
 * @package Serializer
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class SerializerBuilder
{
    private $encoders;
    private $normalizers;
    private $driver;
    private $metadataCache;
    private $debug;
    private $serializerClassDir;
    private $metadataDirs;
    private $serializerClassGenerateStrategy;
    private $instantiator;
    private $format = 'json';
    private $dispatcher;
    private $accessStrategy = 'accessors';
    private $hasListener = false;

    /**
     * Builder constructor.
     */
    public function __construct()
    {
        $this->encoders = new EncoderRegistry();
        $this->normalizers = new NormalizerRegistry();
        $this->debug = false;
        $this->metadataDirs = [];
        $this->serializerClassGenerateStrategy = SerializerClassLoader::AUTOGENERATE_ALWAYS;
    }

    /**
     * @param DriverInterface $driver
     * @return SerializerBuilder
     */
    public function setMetadataDriver(DriverInterface $driver): SerializerBuilder
    {
        $this->driver = $driver;
        return $this;
    }

    public function setMetadataDirs(array $dirs): SerializerBuilder
    {
        $this->metadataDirs = [];
        $this->addMetadataDirs($dirs);
        return $this;
    }

    public function addMetadataDirs(array $dirs): SerializerBuilder
    {
        foreach ($dirs as $namespace => $dir) {
            $this->addMetadataDir($namespace, $dir);
        }

        return $this;
    }

    public function addMetadataDir(string $namespace, string $dir): SerializerBuilder
    {
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException('The metadata directory "' . $dir . '" does not exist');
        }

        $this->metadataDirs[$namespace] = $dir;
        return $this;
    }

    public function setSerializerClassDir(string $dir): SerializerBuilder
    {
        if (!is_dir($dir)) {
            $this->createDir($dir);
        }

        if (!is_writable($dir)) {
            throw new \InvalidArgumentException(sprintf('The serializer class directory "%s" is not writable.', $dir));
        }

        $this->serializerClassDir = $dir;

        return $this;
    }

    public function setDebug(bool $debug): SerializerBuilder
    {
        $this->debug = $debug;
        return $this;
    }

    public function addNormalizer($normalizer): SerializerBuilder
    {
        $this->normalizers->add($normalizer);
        return $this;
    }

    public function enableBuiltInNormalizers(): SerializerBuilder
    {
        $this->normalizers->add(new CollectionNormalizer());
        $this->normalizers->add(new DateTimeNormalizer());
        $this->normalizers->add(new JsonNormalizer());
        $this->normalizers->add(new ScalarNormalizer());
        return $this;
    }

    public function addEncoder(EncoderInterface $encoder): SerializerBuilder
    {
        $this->encoders->add($encoder);
        return $this;
    }

    public function setMetadataCacheDir(string $dir): SerializerBuilder
    {
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException('The metadata cache directory "' . $dir . '" does not exist');
        }

        $this->setMetadataCache(new FileCache($dir));
        return $this;
    }

    public function setMetadataCache(CacheInterface $cache): SerializerBuilder
    {
        $this->metadataCache = $cache;
        return $this;
    }

    public function setSerializerClassGenerateStrategy(int $strategy): SerializerBuilder
    {
        $this->serializerClassGenerateStrategy = $strategy;
        return $this;
    }

    public function enableAnnotations(AnnotationReader $reader = null)
    {
        if (!class_exists(AnnotationReader::class)) {
            throw new \RuntimeException('The annotation reader was not loaded. ' .
                'You must include the package doctrine/annotations as your composer dependency.');
        }

        AnnotationRegistry::registerLoader('class_exists');

        $this->driver = new AnnotationDriver($reader ?? new AnnotationReader(), new TypeGuesser());
        return $this;
    }

    public function setObjectInstantiator(ObjectInstantiatorInterface $instantiator): SerializerBuilder
    {
        $this->instantiator = $instantiator;
        return $this;
    }

    public function addListener(string $eventName, callable $listener, int $priority = 0, string $type = null)
    {
        if (null === $this->dispatcher) {
            $this->dispatcher = new EventDispatcher();
        }

        $this->dispatcher->addListener($eventName, $listener, $priority, $type);
        $this->hasListener = true;
        return $this;
    }

    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        if (null === $this->dispatcher) {
            $this->dispatcher = new EventDispatcher();
        }

        $this->dispatcher->addSubscriber($subscriber);
        $this->hasListener = true;
        return $this;
    }

    /**
     * @param string $format
     * @return SerializerBuilder
     */
    public function setFormat(string $format): SerializerBuilder
    {
        $this->format = $format;
        return $this;
    }

    public function accessThroughAccessors(): SerializerBuilder
    {
        $this->accessStrategy = 'accessors';
        return $this;
    }

    public function accessThroughReflection(): SerializerBuilder
    {
        $this->accessStrategy = 'reflection';
        return $this;
    }

    /**
     * @return SerializerInterface
     */
    public function build(): SerializerInterface
    {
        if ($this->encoders->isEmpty()) {
            $this->encoders->add(new JsonEncoder());
        }

        if (null === $classDir = $this->serializerClassDir) {
            $this->createDir($classDir = sys_get_temp_dir() . '/serializer/classes');
        }

        if (null === $driver = $this->driver) {
            $fileLocator = new FileLocator($this->metadataDirs);
            $driver = new DriverChain([
                new YamlDriver($fileLocator),
                new XmlDriver($fileLocator),
            ]);
        }

        $driver = new ConfiguratorDriver($driver, [
            new TypeConfigurator(),
            new GetterConfigurator(),
            new SetterConfigurator(),
            new DateTimeConfigurator()
        ]);

        $metadataFactory = new MetadataFactory($driver, 'Metadata\ClassHierarchyMetadata', $this->debug);
        if (null !== $this->metadataCache) {
            $metadataFactory->setCache($this->metadataCache);
        }

        $twig = new \Twig_Environment(
            new \Twig_Loader_Filesystem([__DIR__ . '/Resources/templates']),
            [
                'debug' => $this->debug,
                'strict_variables' => true
            ]
        );

        $template = 'accessors.php.twig';

        if ('reflection' === $this->accessStrategy) {
            $template = 'reflection.php.twig';
        }

        $classLoader = new SerializerClassLoader(
            $metadataFactory,
            new SerializerClassCodeGenerator($twig, $template),
            new SerializerClassWriter($classDir),
            $this->serializerClassGenerateStrategy
        );

        if (null === $this->instantiator) {
            $this->instantiator = new DoctrineInstantiator(new Instantiator());
        }
        $this->normalizers->unshift(new ObjectNormalizer($classLoader, $this->instantiator));

        if (null === $this->dispatcher) {
            return new Serializer(
                $this->encoders->get($this->format),
                $this->normalizers
            );
        }

        return new EventEmitterSerializer(
            $this->encoders->get($this->format),
            $this->normalizers,
            $this->dispatcher
        );
    }

    private function createDir($dir)
    {
        if (is_dir($dir)) {
            return;
        }
        if (false === @mkdir($dir, 0777, true) && false === is_dir($dir)) {
            throw new \RuntimeException(sprintf('Could not create directory "%s".', $dir));
        }
    }
}
