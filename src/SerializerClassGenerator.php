<?php

namespace Serializer;

use Metadata\ClassMetadata;

/**
 * Class ObjectSerializerGenerator
 *
 * @package Serializer
 * @author Tales Santos <tales.maxmilhas@gmail.com>
 */
class SerializerClassGenerator
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var TypeRegistryInterface
     */
    private $typeRegistry;

    /**
     * @var bool
     */
    private $debug;

    /**
     * SerializerClassGenerator constructor.
     * @param string $path
     * @param TypeRegistryInterface $typeRegistry
     * @param bool $debug
     */
    public function __construct(string $path, TypeRegistryInterface $typeRegistry, bool $debug = false)
    {
        $this->path = $path;
        $this->typeRegistry = $typeRegistry;
        $this->debug = $debug;
    }

    /**
     * @param ClassMetadata $classMetadata
     * @return SerializerClassInterface
     */
    public function getGeneratorFor(ClassMetadata $classMetadata)
    {
        $filename = $this->getFilename($classMetadata);
        $fqn = $this->getClassName($classMetadata);

        if (!$this->debug && file_exists($filename)) {
            require_once $filename;
            return new $fqn();
        }

        $code =
            $this->classDeclaration($fqn) .
            $this->methodDeclaration($classMetadata) .
            $this->methodBody($classMetadata) .
            $this->endMethod() .
            $this->endClass();


        file_put_contents($filename, $code);
        chmod($filename, 0664);

        require $filename;

        return new $fqn();
    }

    private function getClassName(ClassMetadata $metadata): string
    {
        return str_replace('\\', '', $metadata->name) . 'Serializer';
    }

    private function getFilename(ClassMetadata $metadata): string
    {
        return $this->path . DIRECTORY_SEPARATOR . $this->getClassName($metadata) . '.php';
    }

    private function classDeclaration(string $className): string
    {
        return <<<EOF
<?php

use Metadata\ClassMetadata;
use Serializer\Serializer;
use Serializer\SerializerClassInterface;

/**
 * THIS CLASS WAS GENERATED BY THE SERIALIZER. DO NOT EDIT THIS FILE.
 */
class $className implements SerializerClassInterface
{

EOF;
    }

    private function methodDeclaration(ClassMetadata $metadata): string
    {
        return <<<EOF
    /**
     * @param ClassMetadata \$metadata
     * @param {$metadata->name} \$object
     * @param Serializer \$serializer
     * @return array
     */
    public function serialize(ClassMetadata \$metadata, \$object, Serializer \$serializer): array
    {

EOF;
    }

    private function methodBody(ClassMetadata $metadata): string
    {
        $code = <<<EOF
        \$data = [];


EOF;
        foreach ($metadata->propertyMetadata as $property) {

            $getter = "\$object->{$property->getter}()";
            $value = '$value';

            if ($this->typeRegistry->has($property->type)) {
                $value = $this->typeRegistry->get($property->type)->modify($value, $property);
            }

            $code .= <<<EOF
        #field '$property->name'
        if (null !== \$value = $getter) {
            \$data['$property->exposeAs'] = $value;
        }


EOF;
        }

        $code .= <<<EOF
        return \$data;

EOF;

        return $code;
    }

    private function endMethod(): string
    {
        return <<<EOF
    }

EOF;

    }

    private function endClass(): string
    {
        return <<<EOF
}

EOF;
    }
}
