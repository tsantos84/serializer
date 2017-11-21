<?php

namespace TSantos\Serializer;

use Metadata\ClassMetadata;

/**
 * Class SerializerClassWriter
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class SerializerClassWriter
{
    /**
     * @var string
     */
    private $path;

    /**
     * SerializerClassWriter constructor.
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @param ClassMetadata $classMetadata
     * @param string $code
     * @return bool
     */
    public function write(ClassMetadata $classMetadata, string $code)
    {
        $filename = sprintf('%s/%sSerializer.php', $this->path, str_replace('\\', '', $classMetadata->name));
        return file_put_contents($filename, $code) > 0;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
