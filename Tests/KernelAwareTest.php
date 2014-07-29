<?php


namespace Sparkcentral\Bundle\PSRedisBundle\Tests;

use Sparkcentral\Bundle\PSRedisBundle\DependencyInjection\SparkcentralPSRedisExtension;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class KernelAwareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * @return null
     */
    public function setUp()
    {
        $this->container = new ContainerBuilder();

        $extension = new SparkcentralPSRedisExtension();
        $extension->load(array(), $this->container);

        parent::setUp();
    }

    /**
     * @return null
     */
    public function tearDown()
    {
        unset($this->container);

        parent::tearDown();
    }
}
 