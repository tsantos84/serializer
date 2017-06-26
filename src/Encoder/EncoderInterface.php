<?php

namespace TSantos\Serializer\Encoder;

/**
 * Interface EncoderInterface
 *
 * @package Serializer\Encoder
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
interface EncoderInterface
{
    /**
     * @param array $data
     * @return string
     */
    public function encode(array $data): string;

    /**
     * @return string
     */
    public function getFormat(): string;
}
