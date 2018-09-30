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

namespace Tests\TSantos\Serializer\Fixture\Model;

/**
 * Class DummyPublic.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class DummyPublic extends DummyAbstract implements DummyInterface
{
    public $foo;
    public $bar;
}
