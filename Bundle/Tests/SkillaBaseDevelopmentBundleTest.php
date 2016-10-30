<?php
/**
 * Created by PhpStorm.
 * User: Skilla <sergio.zambrano@gmail.com>
 * Date: 30/10/16
 * Time: 15:16
 */

namespace Skilla\BaseDevelopmentBundle\Tests;

use Skilla\BaseDevelopmentBundle\SkillaBaseDevelopmentBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SkillaBaseDevelopmentBundleTest extends KernelTestCase
{
    public function testInstantiation()
    {
        $expected = 'Skilla\\BaseDevelopmentBundle\\SkillaBaseDevelopmentBundle';
        $actual = new SkillaBaseDevelopmentBundle();
        $this->assertInstanceOf($expected, $actual);
    }
}
