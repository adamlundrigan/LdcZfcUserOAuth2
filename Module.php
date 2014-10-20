<?php
namespace LdcZfcUserApigility;

use Zend\Mvc\MvcEvent;
use ZF\MvcAuth\MvcAuthEvent;
use ZF\MvcAuth\Identity\AuthenticatedIdentity;
use ZfcUser\Service\User as ZfcUserService;
use ZfcUser\Entity\UserInterface as ZfcUserEntity;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        // Short-circuit ZfcUser's inbuilt user session storage mechanism
        // as we're relying solely on zf-mvc-auth to handle that for us
        $storage = new \Zend\Authentication\Storage\NonPersistent();        
        $sm = $e->getApplication()->getServiceManager();
        $sm->get('ZfcUser\Authentication\Storage\Db')->setStorage($storage);
        $sm->get('ZfcUser\Authentication\Adapter\Db')->setStorage($storage);
        
        // Inject authenticated user from zf-mvc-auth into ZfcUser so it's
        // built-in session and user checking still function properly
        $zfcUserService = $sm->get('zfcuser_user_service');
        $em = $e->getApplication()->getEventManager();
        $em->attach(MvcAuthEvent::EVENT_AUTHENTICATION_POST, function (MvcAuthEvent $e) use ($zfcUserService, $storage) {
            $identity = $e->getIdentity();
            if ( ! $identity instanceof AuthenticatedIdentity ) {
                return;
            }
            
            $token = $identity->getAuthenticationIdentity();
            $uid   = $token['user_id'];
            
            $user = $zfcUserService->getUserMapper()->findById($uid);
            if ( ! $user instanceof ZfcUserEntity ) {
                return;
            }
            
            $storage->write($user->getId());
        });
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
