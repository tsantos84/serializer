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
 * Interface CacheableNormalizerInterface.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
interface CacheableNormalizerInterface
{
    /**
     * @return bool
     */
    public function canBeCachedByType(): bool;
}
