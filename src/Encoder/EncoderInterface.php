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

namespace TSantos\Serializer\Encoder;

/**
 * Interface EncoderInterface.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
interface EncoderInterface
{
    /**
     * @param array $data
     *
     * @return string
     */
    public function encode(array $data): string;

    /**
     * @param string $content
     *
     * @return array
     */
    public function decode(string $content): array;

    /**
     * @return string
     */
    public function getFormat(): string;
}
