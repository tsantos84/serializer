<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\Common\Annotations\AnnotationRegistry;

if (class_exists('Doctrine\Common\Annotations\AnnotationRegistry', false)) {
    call_user_func(function () {
        if (!is_file($autoloadFile = __DIR__ . '/../vendor/autoload.php')) {
            throw new \RuntimeException('Did not find vendor/autoload.php. Did you run "composer install --dev"?');
        }
        require $autoloadFile;

        AnnotationRegistry::registerLoader('class_exists');
    });
}
