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

namespace Tests\TSantos\Serializer;

use PHPUnit\Framework\TestCase;
use TSantos\Serializer\SerializationContext;

/**
 * Class SerializationContextTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class SerializationContextTest extends TestCase
{
    /** @test */
    public function it_should_not_throw_circular_reference_exception()
    {
        $context = new SerializationContext();
        $subject = new class() {
        };
        $context->enter($subject);
        $context->leave($subject);
        $context->enter($subject);
        $context->leave($subject);
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function it_should_throw_circular_reference_exception()
    {
        $this->expectException(\TSantos\Serializer\Exception\CircularReferenceException::class);

        $context = new SerializationContext();
        $subject = new class() {
        };
        $context->enter($subject);
        $context->enter($subject);
    }
}
