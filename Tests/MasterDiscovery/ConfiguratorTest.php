<?php


namespace Sparkcentral\Bundle\PSRedisBundle\Tests\MasterDiscovery;


use PSRedis\MasterDiscovery;
use Sparkcentral\Bundle\PSRedisBundle\Tests\KernelAwareTest;

class ConfiguratorTest extends KernelAwareTest
{
    public function testThatMasterDiscoveryConfiguratorIsTheCorrectClass()
    {
        $masterDiscoveryConfigurator = $this->container->get('sparkcentral.psredis_bundle.master_discovery.configurator');
        $this->assertInstanceOf(
            '\\Sparkcentral\\Bundle\\PSRedisBundle\\MasterDiscovery\\Configurator',
            $masterDiscoveryConfigurator,
            'Verify the MasterDiscovery Configurator dependency has the correct object class'
        );
    }

    public function testThatTheConfiguratorSetsSentinelParameters()
    {
        $masterDiscoveryConfigurator = $this->container->get('sparkcentral.psredis_bundle.master_discovery.configurator');
        $this->assertAttributeEquals(
            array(
                array('address' => '127.0.0.1', 'port' => 26379),
                array('address' => '127.0.0.1', 'port' => 26380),
                array('address' => '127.0.0.1', 'port' => 26381),
            ),
            'sentinelParametersCollection',
            $masterDiscoveryConfigurator,
            'The configurator knows the sentinel parameters'
        );
    }

    public function testThatMasterDiscoveryConfiguratorSetsBackoffParameters()
    {
        $masterDiscoveryConfigurator = $this->container->get('sparkcentral.psredis_bundle.master_discovery.configurator');
        $this->assertAttributeEquals(
            array(
                'strategy'          => 'Incremental',
                'offset'            => 500,
                'multiplier'        => 1.5,
                'maximum_attempts'  => 10
            ),
            'backoffParameters',
            $masterDiscoveryConfigurator,
            'The configurator knows the backoff parameters'
        );
    }

    public function testThatMasterDiscoveryIsTheCorrectClass()
    {
        $masterDiscovery = $this->container->get('sparkcentral.psredis_bundle.master_discovery');
        $this->assertInstanceOf(
            '\\PSRedis\\MasterDiscovery',
            $masterDiscovery,
            'Verify the MasterDiscovery dependency has the correct object class'
        );
    }

    public function testThatSentinelsAreConfiguredOnMasterDiscoveryObject()
    {
        $masterDiscovery = $this->container->get('sparkcentral.psredis_bundle.master_discovery');
        $this->assertAttributeCount(3, 'sentinels', $masterDiscovery, '3 sentinels were added by the configurator');
    }

    public function testThatBackoffIsConfiguredOnMasterDiscoveryObject()
    {
        $expectedBackoff = new MasterDiscovery\BackoffStrategy\Incremental(500, 1.5);
        $expectedBackoff->setMaxAttempts(10);

        /** @var MasterDiscovery $masterDiscovery */
        $masterDiscovery = $this->container->get('sparkcentral.psredis_bundle.master_discovery');
        $this->assertAttributeEquals(
            $expectedBackoff,
            'backoffStrategy',
            $masterDiscovery,
            'Verify the backoff was configured properly'
        );
    }

    public function testThatHAClientIsConfiguredProperly()
    {
        $HAClient = $this->container->get('sparkcentral.psredis_bundle.ha_client');
        $this->assertAttributeInstanceOf(
            '\\PSRedis\\MasterDiscovery', 'masterDiscovery', $HAClient, 'Verify the master discovery is configured in HAClient'
        );
    }
}
 