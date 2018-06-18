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

use Metadata\MergeableClassMetadata;

/**
 * Class ClassMetadata
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class ClassMetadata extends MergeableClassMetadata
{
    public $baseClass;

    public function serialize()
    {
        return serialize(array(
            $this->name,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->createdAt,
            $this->baseClass
        ));
    }

    public function unserialize($str)
    {
        list(
            $this->name,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->createdAt,
            $this->baseClass
        ) = unserialize($str);

        $this->reflection = new \ReflectionClass($this->name);
    }
}
