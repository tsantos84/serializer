<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Serializer\Encoder;

/**
 * Class JsonEncoder
 *
 * @package Serializer\Encoder
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class JsonEncoder implements EncoderInterface
{
    /**
     * @param array $data
     * @return string
     */
    public function encode(array $data): string
    {
        return json_encode($data);
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return 'json';
    }
}
