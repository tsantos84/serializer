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

use TSantos\Serializer\Metadata\ClassMetadata;

/**
 * Class HydratorCompiler
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
interface HydratorCompilerInterface
{
    /**
     * Generate hydrators code and save the class in the specified directory
     *
     * @param ClassMetadata $classMetadata
     */
    public function compile(ClassMetadata $classMetadata): void;
}
