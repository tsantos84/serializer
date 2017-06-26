<?php

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
