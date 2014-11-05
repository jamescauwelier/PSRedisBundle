<?php

namespace Sparkcentral\Bundle\PSRedisBundle\Symfony\Component\HttpFoundation\Session\Storage\Handler;

use PSRedis\HAClient;

/**
 * Class PSRedisSessionHandler
 *
 * Symfony2 session handler to allow session storage on redis using the HA sentinel library
 */
class PSRedisSessionHandler implements \SessionHandlerInterface
{
    /**
     * @var HAClient
     */
    private $redisClient;

    /**
     * @var int how quickly data in the session should expire
     */
    private $ttlInSeconds;

    /**
     * @param HAClient $redisClient
     */
    public function __construct(HAClient $redisClient, $ttlInSeconds)
    {
        $this->redisClient = $redisClient;
        $this->ttlInSeconds = empty($ttlInSeconds) ? 60 : $ttlInSeconds;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        return (bool) $this->redisClient->del($sessionId);
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxlifetime)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionId)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        return $this->redisClient->get($sessionId);
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $sessionData)
    {
        return (bool) $this->redisClient->setex($sessionId, $this->ttlInSeconds, $sessionData);
    }
}