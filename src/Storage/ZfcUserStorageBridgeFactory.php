<?php
namespace LdcZfcUserOAuth2\Storage;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ZfcUserStorageBridgeFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $obj = new ZfcUserStorageBridge(
            $serviceLocator->get('zfcuser_user_mapper'),
            $serviceLocator->get('zfcuser_auth_service'),
            $serviceLocator->get('ZfcUser\Authentication\Adapter\AdapterChain'),
            $serviceLocator->get('zfcuser_module_options')
        );

        return $obj;
    }
}
