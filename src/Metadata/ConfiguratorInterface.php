<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Serializer\Metadata;

/**
 * Interface ConfiguratorInterface
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
interface ConfiguratorInterface
{
    /**
     * @param ClassMetadata $classMetadata
     */
    public function configure(ClassMetadata $classMetadata): void;
}
