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

use Metadata\ClassMetadata;

/**
 * Class ClassWriter
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class HydratorCodeWriter
{
    /**
     * @var string
     */
    private $path;

    /**
     * SerializerClassWriter constructor.
     * @param string $path
     */
    public function __construct(string $path)
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
        $filename = sprintf('%s/%sHydrator.php', $this->path, str_replace('\\', '', $classMetadata->name));
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
