<?php
namespace LdcZfcUserOAuth2Test\Storage;

use LdcZfcUserOAuth2Test\TestCase;
use LdcZfcUserOAuth2\Storage\ZfcUserPdo;

class ZfcUserPdoTest extends TestCase
{
    public function setUp()
    {
        $this->pdo = \Mockery::mock('PDO');
        $this->pdo->shouldIgnoreMissing();
        $this->bridge = \Mockery::mock('LdcZfcUserOAuth2\Storage\ZfcUserStorageBridge');

        $this->service = new ZfcUserPdo($this->pdo, [], $this->bridge);
    }

    public function testCheckUserCredentialsProxiesToBridge()
    {
        $this->bridge->shouldReceive('checkUserCredentials')
                     ->withArgs(['foo', 'bar'])
                     ->andReturn(false)
                     ->once();

        $this->assertFalse($this->service->checkUserCredentials('foo', 'bar'));
    }

    public function testGetUserDetailsProxiesToBridge()
    {
        $result = ['id' => 'foo'];

        $this->bridge->shouldReceive('getUserDetails')
                     ->withArgs(['foo'])
                     ->andReturn($result)
                     ->once();

        $this->assertEquals($result, $this->service->getUserDetails('foo'));
    }
}
