<?php
namespace LdcZfcUserApigility;

use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $sm->get('ZfcUser\Authentication\Storage\Db')->setStorage(
            new \Zend\Authentication\Storage\NonPersistent()
        );
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src'
                ),
            ),
        );
    }
}
