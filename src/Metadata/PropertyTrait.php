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

namespace TSantos\Serializer\Metadata;

/**
 * Class AbstractPropertyMetadata.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
trait PropertyTrait
{
    public $type;
    public $exposeAs;
    public $groups = ['Default'];
    public $readValueFilter;
    public $options = [];

    public function isScalarType(): bool
    {
        return \in_array($this->type, ['integer', 'string', 'float', 'boolean'], true);
    }

    public function isScalarCollectionType(): bool
    {
        if (false === $pos = \mb_strpos($this->type, '[]')) {
            return false;
        }

        $type = \mb_substr($this->type, 0, $pos);

        return \in_array($type, ['integer', 'string', 'float', 'boolean'], true);
    }

    public function isMixedCollectionType(): bool
    {
        return 'mixed' === $this->getTypeOfCollection() || '[]' === $this->type;
    }

    public function isCollection(): bool
    {
        return false !== \mb_strpos($this->type, '[]');
    }

    public function getTypeOfCollection(): ?string
    {
        if (false === $pos = \mb_strpos($this->type, '[]')) {
            return null;
        }

        return \mb_substr($this->type, 0, $pos);
    }
}
