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
 * Interface ClassMetadataAwareInterface.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
interface ClassMetadataAwareInterface
{
    /**
     * @param ClassMetadata $classMetadata
     */
    public function setClassMetadata(ClassMetadata $classMetadata): void;
}
