<?php
/**
 * Created by PhpStorm.
 * User: Skilla <sergio.zambrano@gmail.com>
 * Date: 1/11/16
 * Time: 16:54
 */

namespace Skilla\BaseDevelopmentBundle\Tests\Controller;

use \Skilla\BaseDevelopmentBundle\Controller\DefaultController;
use \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DefaultControllerTest extends KernelTestCase
{
    public function testInstantiation()
    {
        $expected = 'Symfony\\Bundle\\FrameworkBundle\\Controller\\Controller';
        $actual = new DefaultController();
        $this->assertInstanceOf($expected, $actual);
    }

    public function testIndex()
    {
        self::bootKernel();
        $sut = new DefaultController();
        $sut->setContainer(self::$kernel->getContainer());
        $response = $sut->indexAction();
        $this->assertEquals('"Homepage"', $response->getContent());
        parent::tearDown();
    }
}
