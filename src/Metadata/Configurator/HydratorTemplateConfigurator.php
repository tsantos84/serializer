<?php
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

/**
 * Class HydratorTemplateConfigurator
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class HydratorTemplateConfigurator implements ConfiguratorInterface
{
    /**
     * @var string
     */
    private $defaultTemplate;

    /**
     * HydratorTemplateConfigurator constructor.
     * @param string $defaultTemplate
     */
    public function __construct(string $defaultTemplate)
    {
        $this->defaultTemplate = $defaultTemplate;
    }

    public function configure(ClassMetadata $classMetadata): void
    {
        if (null === $classMetadata->template) {
            $classMetadata->template = $this->defaultTemplate;
        }
    }
}
