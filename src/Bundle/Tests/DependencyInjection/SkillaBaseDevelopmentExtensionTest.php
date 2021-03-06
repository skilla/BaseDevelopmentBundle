<?php
/**
 * Created by PhpStorm.
 * User: Skilla <sergio.zambrano@gmail.com>
 * Date: 8/10/16
 * Time: 21:07
 */

namespace Skilla\BaseDevelopmentBundle\Tests\DependencyInjection;

use \Skilla\BaseDevelopmentBundle\DependencyInjection\SkillaBaseDevelopmentExtension;
use \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use \Symfony\Component\DependencyInjection\ContainerBuilder;
use \Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class SkillaBaseDevelopmentExtensionTest extends KernelTestCase
{
    public function testInstantiation()
    {
        $expected = 'Symfony\\Component\\HttpKernel\\DependencyInjection\\Extension';
        $actual = new SkillaBaseDevelopmentExtension();
        $this->assertInstanceOf($expected, $actual);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLoadWhitNoKernelParameter()
    {
        $parameters = array();
        $parameterBag = new ParameterBag($parameters);
        $containerBuilder = new ContainerBuilder($parameterBag);
        $sut = new SkillaBaseDevelopmentExtension();

        $sut->load(array(array()), $containerBuilder);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLoadWhitNoBundleParameter()
    {
        $parameters = $this->getBasicParameters();
        $parameterBag = new ParameterBag($parameters);
        $containerBuilder = new ContainerBuilder($parameterBag);
        $sut = new SkillaBaseDevelopmentExtension();

        $configs = array(array());
        $sut->load($configs, $containerBuilder);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLoadWithInvalidParameter()
    {
        $parameters = $this->getBasicParameters();
        $parameterBag = new ParameterBag($parameters);
        $containerBuilder = new ContainerBuilder($parameterBag);
        $sut = new SkillaBaseDevelopmentExtension();

        $configs = array(
            array(
                'type' => null,
            )
        );
        $sut->load($configs, $containerBuilder);
    }

    public function testLoad()
    {
        static::bootKernel();

        $parameters = array(
            'kernel.root_dir' => static::$kernel->getRootDir(),
            'kernel.debug' => static::$kernel->isDebug(),
        );
        $parameterBag = new ParameterBag($parameters);
        $containerBuilder = new ContainerBuilder($parameterBag);
        $sut = new SkillaBaseDevelopmentExtension();

        $configs = array(
            array(
                'key' => 'value',
            )
        );
        $sut->load($configs, $containerBuilder);
        $this->assertEquals('skilla_base_development', $sut->getAlias());
        parent::tearDown();
    }

    /**
     * @return array
     */
    private function getBasicParameters()
    {
        return array(
            'kernel.root_dir' => realpath(__DIR__ . '/app/'),
            'kernel.debug' => false,
        );
    }
}
