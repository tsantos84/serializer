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

/**
 * Class TypeHelper.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class TypeHelper
{
    private static $scalarTypes = [
        'integer',
        'int',
        'string',
        'float',
        'boolean',
        'bool',
    ];

    public static function isScalar(string $type): bool
    {
        return \in_array($type, self::$scalarTypes, true);
    }

    public static function getChecker(string $type, string $value): string
    {
        if ('boolean' === $type) {
            $type = 'bool';
        }

        if (self::isScalar($type)) {
            return \sprintf('\is_%s(%s)', $type, $value);
        }

        return \sprintf('%s instanceof \%s', $value, \ltrim($type, '\\'));
    }

    public static function isScalarCollectionType(string $type): bool
    {
        if (false === $pos = \mb_strpos($type, '[]')) {
            return false;
        }

        $type = \mb_substr($type, 0, $pos);

        return self::isScalar($type);
    }

    public static function isMixedCollectionType(string $type): bool
    {
        return 'mixed' === self::getTypeOfCollection($type) || '[]' === $type;
    }

    public static function isCollection(string $type): bool
    {
        return false !== \mb_strpos($type, '[]');
    }

    public static function getTypeOfCollection(string $type): ?string
    {
        if (false === $pos = \mb_strpos($type, '[]')) {
            return null;
        }

        return \mb_substr($type, 0, $pos);
    }
}
