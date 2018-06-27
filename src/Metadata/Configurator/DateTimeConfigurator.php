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

namespace TSantos\Serializer\Metadata\Configurator;

use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\Metadata\ConfiguratorInterface;
use TSantos\Serializer\Metadata\PropertyMetadata;

/**
 * Class DateTimeConfigurator.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class DateTimeConfigurator implements ConfiguratorInterface
{
    /**
     * @var string
     */
    private $defaultFormat;

    public function __construct(string $defaultFormat = \DateTime::ISO8601)
    {
        $this->defaultFormat = $defaultFormat;
    }

    public function configure(ClassMetadata $classMetadata): void
    {
        /** @var PropertyMetadata $propertyMetadata */
        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {
            if (\DateTime::class !== $propertyMetadata->type) {
                continue;
            }

            $format = $propertyMetadata->options['format'] ?? $this->defaultFormat;

            if (null === $propertyMetadata->readValueFilter) {
                $propertyMetadata->readValueFilter = sprintf('$value->format(\'%s\')', $format);
            }

            if (null === $propertyMetadata->writeValueFilter) {
                $propertyMetadata->writeValueFilter = sprintf('\DateTime::createFromFormat(\'%s\', $value)', $format);
            }
        }
    }
}
