<?php

namespace Sparkcentral\Bundle\PSRedisBundle\Tests\Symfony\Component\HttpFoundation\Session\Storage\Handler;

use PSRedis\HAClient;
use PSRedis\MasterDiscovery;
use Sparkcentral\Bundle\PSRedisBundle\Symfony\Component\HttpFoundation\Session\Storage\Handler\PSRedisSessionHandler;

class PSRedisSessionHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * constructs the minimal session handler that won't throw exceptions before it is called
     * @return PSRedisSessionHandler
     */
    private function getSessionHandler()
    {
        $redisMasterDiscovery = new MasterDiscovery('test');
        $redisClient = new HAClient($redisMasterDiscovery);

        return new PSRedisSessionHandler($redisClient, 60);
    }

    /**
     * @param HAClient $mockedRedisClient
     * @return PSRedisSessionHandler
     */
    private function getSessionHandlerWithMockedClient(HAClient $mockedRedisClient)
    {
        return new PSRedisSessionHandler($mockedRedisClient, 60);
    }

    /**
     * We test that open always returns true because the save path is not important for storing sessions in redis
     */
    public function testThatWeCanOpenASession()
    {
        $openResult = $this->getSessionHandler()->open("", "");
        $this->assertTrue($openResult, "Test that ->open(...) always returns true");
    }

    /**
     * Closing a session is not needed if we don't initialize anything on opening, so we're always expecting true here
     */
    public function testThatWeCanCloseASession()
    {
        $closeResult = $this->getSessionHandler()->close();
        $this->assertTrue($closeResult, "Test that ->close() always returns true");
    }

    public function testThatWeCanWriteToASession()
    {
        $redisclientMock = $this->getMock('PSRedis\HAClient', array('setex'), array(new MasterDiscovery('test')));
        $redisclientMock
            ->expects($this->once())
            ->method('setex')
            ->with("123", 60, "boom")
            ->will($this->returnValue(true));

        $writeResult = $this->getSessionHandlerWithMockedClient($redisclientMock)->write("123", "boom");
        $this->assertTrue($writeResult, "Test that the result of writing to session comes from the redis client");
    }

    public function testThatWeCanReadFromASession()
    {
        $redisclientMock = $this->getMock('PSRedis\HAClient', array('get'), array(new MasterDiscovery('test')));
        $redisclientMock
            ->expects($this->once())
            ->method('get')
            ->with("123")
            ->will($this->returnValue("boom"));

        $readResult = $this->getSessionHandlerWithMockedClient($redisclientMock)->read("123");
        $this->assertEquals("boom", $readResult, "Test that we are reading the session result from the redis client");
    }

    public function testThatWeCanDestroyASessionKeyValuePair()
    {
        $redisclientMock = $this->getMock('PSRedis\HAClient', array('del'), array(new MasterDiscovery('test')));
        $redisclientMock
            ->expects($this->once())
            ->method('del')
            ->with("123")
            ->will($this->returnValue(1));

        $destroyResult = $this->getSessionHandlerWithMockedClient($redisclientMock)->destroy("123");
        $this->assertTrue($destroyResult, "Verify that the redis client is used to delete keys");
    }

    public function testThatWeCanRunGarbageCollectionOnSessions()
    {
        $gcResult = $this->getSessionHandler()->gc(0);
        $this->assertTrue($gcResult, "Garbage collection should return true all the time (we're not implementing it)");
    }
}
 