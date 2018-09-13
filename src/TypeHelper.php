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

/**
 * Class TypeHelper
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
        'bool'
    ];

    public static function isScalar(string $type): bool
    {
        return \in_array($type, self::$scalarTypes, true);
    }

    public static function getScalarChecker(string $type): string
    {
        if (!self::isScalar($type)) {
            return null;
        }

        if ('boolean' === $type) {
            $type = 'bool';
        }

        $checker = '\is_' . $type;

        if (!function_exists($checker)) {
            return null;
        }

        return $checker;
    }

}
