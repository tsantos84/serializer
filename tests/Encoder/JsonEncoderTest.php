<?php

namespace Tests\TSantos\Serializer\Encoder;

use TSantos\Serializer\Encoder\JsonEncoder;
use PHPUnit\Framework\TestCase;

class JsonEncoderTest extends TestCase
{
    public function testEncode()
    {
        $encoder = new JsonEncoder();
        $this->assertEquals('{"foo":"bar"}', $encoder->encode(['foo' => 'bar']));
    }

    public function testGetFormat()
    {
        $encoder = new JsonEncoder();
        $this->assertEquals('json', $encoder->getFormat());
    }
}
