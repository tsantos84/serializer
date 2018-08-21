<?php

declare(strict_types=1);

/*
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Serializer;

use TSantos\Serializer\Metadata\ClassMetadata;

/**
 * Class ClassWriter.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class HydratorCodeWriter
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * HydratorCodeWriter constructor.
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param ClassMetadata $classMetadata
     * @param string        $code
     *
     * @return bool
     */
    public function write(ClassMetadata $classMetadata, string $code)
    {
        return \file_put_contents($this->configuration->getFilename($classMetadata), $code) > 0;
    }
}
