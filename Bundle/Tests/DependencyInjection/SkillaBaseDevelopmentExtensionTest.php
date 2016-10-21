<?php
/**
 * Created by PhpStorm.
 * User: Skilla <sergio.zambrano@gmail.com>
 * Date: 8/10/16
 * Time: 21:07
 */

namespace Skilla\BaseDevelopmentBundle\Tests\DependencyInjection;


use \Skilla\BaseDevelopmentBundle\DependencyInjection\SkillaBaseDevelopmentExtension;
use \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use \Symfony\Component\DependencyInjection\ContainerBuilder;
use \Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use \Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class EixComercialPersistCacheExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $expected = 'Skilla\\BaseDevelopmentBundle\\DependencyInjection\\SkillaBaseDevelopmentExtension';
        $actual = new SkillaBaseDevelopmentExtension();
        $this->assertInstanceOf($expected, $actual);
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException
     */
    public function testLoadWhitNoKernelParameter()
    {
        $parameters = [];
        $parameterBag = new ParameterBag($parameters);
        $containerBuilder = new ContainerBuilder($parameterBag);
        $sut = new SkillaBaseDevelopmentExtension();

        $sut->load([[]], $containerBuilder);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLoadWhitNoBundleParameter()
    {
        $parameters = [
            'kernel.root_dir' => realpath(__DIR__.'/app/'),
            'kernel.debug' => false,
        ];
        $parameterBag = new ParameterBag($parameters);
        $containerBuilder = new ContainerBuilder($parameterBag);
        $sut = new SkillaBaseDevelopmentExtension();

        $configs = [[]];
        $sut->load($configs, $containerBuilder);
    }

    public function testLoad()
    {
        $parameters = [
            'kernel.root_dir' => realpath(__DIR__.'/app/'),
            'kernel.debug' => false,
        ];
        $parameterBag = new ParameterBag($parameters);
        $containerBuilder = new ContainerBuilder($parameterBag);
        $actual = new SkillaBaseDevelopmentExtension();

        $configs = [
            [
                'type' => null,
            ]
        ];
        $actual->load($configs, $containerBuilder);
    }
}
