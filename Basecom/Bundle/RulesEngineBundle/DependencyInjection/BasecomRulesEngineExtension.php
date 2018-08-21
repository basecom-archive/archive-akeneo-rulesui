<?php

namespace Basecom\Bundle\RulesEngineBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * BasecomRulesEngineExtension.
 *
 * @author Peter van der Zwaag <vanderzwaag@basecom.de>
 */
class BasecomRulesEngineExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $config    An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @api
     *
     * @throws \Exception
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('form_types.yml');
        $loader->load('controllers.yml');
    }
}
