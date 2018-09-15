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

use TSantos\Serializer\TypeHelper;

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
        return TypeHelper::isScalar($this->type);
    }

    public function isScalarCollectionType(): bool
    {
        return TypeHelper::isScalarCollectionType($this->type);
    }

    public function isMixedCollectionType(): bool
    {
        return TypeHelper::isMixedCollectionType($this->type);
    }

    public function isCollection(): bool
    {
        return TypeHelper::isCollection($this->type);
    }

    public function getTypeOfCollection(): ?string
    {
        return TypeHelper::getTypeOfCollection($this->type);
    }
}
