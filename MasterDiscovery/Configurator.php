<?php


namespace Sparkcentral\Bundle\PSRedisBundle\MasterDiscovery;


use PSRedis\Client;
use PSRedis\MasterDiscovery;

class Configurator
{
    private $sentinelParametersCollection;

    private $backoffParameters;

    public function setSentinelParametersCollection(array $sentinelParameters)
    {
        $this->sentinelParametersCollection = $sentinelParameters;
    }

    public function setBackoffParameters(array $backoffParameters)
    {
        $this->backoffParameters = $backoffParameters;
    }

    public function configure(MasterDiscovery $masterDiscovery)
    {
        $this->configureSentinels($masterDiscovery);
        $this->configureBackoffStrategy($masterDiscovery);
    }

    /**
     * @param MasterDiscovery $masterDiscovery
     */
    private function configureSentinels(MasterDiscovery $masterDiscovery)
    {
        foreach ($this->sentinelParametersCollection as $sentinelParameters) {
            $sentinel = new Client($sentinelParameters['address'], $sentinelParameters['port']);
            $masterDiscovery->addSentinel($sentinel);
        }
    }

    /**
     * @param MasterDiscovery $masterDiscovery
     */
    private function configureBackoffStrategy(MasterDiscovery $masterDiscovery)
    {
        $backoffStrategy = new MasterDiscovery\BackoffStrategy\Incremental(
            $this->backoffParameters['offset'], $this->backoffParameters['multiplier']
        );
        $backoffStrategy->setMaxAttempts($this->backoffParameters['maximum_attempts']);
        $masterDiscovery->setBackoffStrategy($backoffStrategy);
    }
}