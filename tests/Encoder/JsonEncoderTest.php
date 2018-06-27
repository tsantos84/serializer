<?php

declare(strict_types=1);
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\TSantos\Serializer\Encoder;

use PHPUnit\Framework\TestCase;
use TSantos\Serializer\Encoder\JsonEncoder;

class JsonEncoderTest extends TestCase
{
    /**
     * @test
     */
    public function encode()
    {
        $encoder = new JsonEncoder();
        $this->assertEquals('{"foo":"bar"}', $encoder->encode(['foo' => 'bar']));
    }

    /**
     * @test
     */
    public function getFormat()
    {
        $encoder = new JsonEncoder();
        $this->assertEquals('json', $encoder->getFormat());
    }
}
