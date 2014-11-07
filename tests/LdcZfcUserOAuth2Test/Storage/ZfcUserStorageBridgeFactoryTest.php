<?php
namespace LdcZfcUserOAuth2Test\Storage;

use LdcZfcUserOAuth2Test\TestCase;
use LdcZfcUserOAuth2\Storage\ZfcUserStorageBridgeFactory;

class ZfcUserStorageBridgeFactoryTest extends TestCase
{
    public function testCreateServiceHappyCase()
    {
        $sm = \Mockery::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $sm->shouldReceive('get')->with('zfcuser_user_mapper')->once()->andReturn(\Mockery::mock('ZfcUser\Mapper\UserInterface'));
        $sm->shouldReceive('get')->with('zfcuser_auth_service')->once()->andReturn(\Mockery::mock('Zend\Authentication\AuthenticationServiceInterface'));
        $sm->shouldReceive('get')->with('ZfcUser\Authentication\Adapter\AdapterChain')->once()->andReturn(\Mockery::mock('ZfcUser\Authentication\Adapter\AdapterChain'));
        $sm->shouldReceive('get')->with('zfcuser_module_options')->once()->andReturn(\Mockery::mock('ZfcUser\Options\ModuleOptions'));

        $factory = new ZfcUserStorageBridgeFactory();
        $obj = $factory->createService($sm);

        $this->assertInstanceOf('LdcZfcUserOAuth2\Storage\ZfcUserStorageBridge', $obj);
    }
}
