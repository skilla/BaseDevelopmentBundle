<?php
/**
 * Created by PhpStorm.
 * User: Skilla <sergio.zambrano@gmail.com>
 * Date: 8/10/16
 * Time: 16:58
 */

namespace Skilla\BaseDevelopmentBundle\DependencyInjection;

use \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use \Symfony\Component\Config\FileLocator;
use \Symfony\Component\HttpKernel\DependencyInjection\Extension;
use \Symfony\Component\DependencyInjection\ContainerBuilder;
use \Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SkillaBaseDevelopmentExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configDir = realpath(__DIR__ . '/../Resources/config') . '/';
        $configFile = 'config.yml';

        $loader = new YamlFileLoader($container, new FileLocator($configDir));
        $loader->load($configFile);

        if (count($configs[0])===0) {
            $exception = new InvalidConfigurationException();
            $exception->addHint(
                sprintf(
                    'Add configuration for "%s" in %s/config/config.yml',
                    $this->getAlias(),
                    $container->getParameter('kernel.root_dir')
                )
            );
            throw $exception;
        }
    }
}
