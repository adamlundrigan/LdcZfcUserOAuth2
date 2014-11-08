<?php

namespace LdcZfcUserOAuth2Test\Authentication\Adapter;

use LdcZfcUserOAuth2\Authentication\Adapter\Db;

class DbTest extends \PHPUnit_Framework_TestCase
{

    public function testGetStorageWillInstantiateNonPersistentAdapterByDefault()
    {
        $db = new Db();
        $this->assertInstanceOf('\Zend\Authentication\Storage\NonPersistent', $db->getStorage());
    }

    // Everything below here is taken from ZfcUserTest\Authentication\Adapter


    /**
     * The object to be tested.
     *
     * @var Db
     */
    protected $db;

    /**
     * Mock of AuthEvent.
     *
     * @var authEvent
     */
    protected $authEvent;

    /**
     * Mock of Storage.
     *
     * @var storage
     */
    protected $storage;

    /**
     * Mock of Options.
     *
     * @var options
     */
    protected $options;

    /**
     * Mock of Mapper.
     *
     * @var mapper
     */
    protected $mapper;

    /**
     * Mock of User.
     *
     * @var user
     */
    protected $user;

    protected function setUp()
    {
        $storage = $this->getMock('Zend\Authentication\Storage\Session');
        $this->storage = $storage;

        $authEvent = $this->getMock('ZfcUser\Authentication\Adapter\AdapterChainEvent');
        $this->authEvent = $authEvent;

        $options = $this->getMock('ZfcUser\Options\ModuleOptions');
        $this->options = $options;

        $mapper = $this->getMock('ZfcUser\Mapper\User');
        $this->mapper = $mapper;

        $user = $this->getMock('ZfcUser\Entity\User');
        $this->user = $user;

        $this->db = new Db();
        $this->db->setStorage($this->storage);
    }

    /**
     * @covers LdcZfcUserOAuth2\Authentication\Adapter\Db::logout
     */
    public function testLogout()
    {
        $this->storage->expects($this->once())
                      ->method('clear');

         $this->db->logout($this->authEvent);
    }

    /**
     * @covers LdcZfcUserOAuth2\Authentication\Adapter\Db::Authenticate
     */
    public function testAuthenticateWhenSatisfies()
    {
        $this->authEvent->expects($this->once())
                        ->method('setIdentity')
                        ->with('ZfcUser')
                        ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once())
                        ->method('setCode')
                        ->with(\Zend\Authentication\Result::SUCCESS)
                        ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once())
                        ->method('setMessages')
                        ->with(array('Authentication successful.'))
                        ->will($this->returnValue($this->authEvent));

        $this->storage->expects($this->at(0))
            ->method('read')
            ->will($this->returnValue(array('is_satisfied' => true)));
        $this->storage->expects($this->at(1))
            ->method('read')
            ->will($this->returnValue(array('identity' => 'ZfcUser')));

        $result = $this->db->authenticate($this->authEvent);
        $this->assertNull($result);
    }

    /**
     * @covers LdcZfcUserOAuth2\Authentication\Adapter\Db::Authenticate
     */
    public function testAuthenticateNoUserObject()
    {
        $this->setAuthenticationCredentials();

        $this->options->expects($this->once())
            ->method('getAuthIdentityFields')
            ->will($this->returnValue(array()));

        $this->authEvent->expects($this->once())
            ->method('setCode')
            ->with(\Zend\Authentication\Result::FAILURE_IDENTITY_NOT_FOUND)
            ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once(1))
            ->method('setMessages')
            ->with(array('A record with the supplied identity could not be found.'))
            ->will($this->returnValue($this->authEvent));

        $this->db->setOptions($this->options);

        $result = $this->db->authenticate($this->authEvent);

        $this->assertFalse($result);
        $this->assertFalse($this->db->isSatisfied());
    }

    /**
     * @covers LdcZfcUserOAuth2\Authentication\Adapter\Db::Authenticate
     */
    public function testAuthenticationUserStateEnabledUserButUserStateNotInArray()
    {
        $this->setAuthenticationCredentials();
        $this->setAuthenticationUser();

        $this->options->expects($this->once())
            ->method('getEnableUserState')
            ->will($this->returnValue(true));
        $this->options->expects($this->once())
            ->method('getAllowedLoginStates')
            ->will($this->returnValue(array(2, 3)));

        $this->authEvent->expects($this->once())
            ->method('setCode')
            ->with(\Zend\Authentication\Result::FAILURE_UNCATEGORIZED)
            ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once())
            ->method('setMessages')
            ->with(array('A record with the supplied identity is not active.'))
            ->will($this->returnValue($this->authEvent));

        $this->user->expects($this->once())
            ->method('getState')
            ->will($this->returnValue(1));

        $this->db->setMapper($this->mapper);
        $this->db->setOptions($this->options);

        $result = $this->db->authenticate($this->authEvent);

        $this->assertFalse($result);
        $this->assertFalse($this->db->isSatisfied());
    }

    /**
     * @covers LdcZfcUserOAuth2\Authentication\Adapter\Db::Authenticate
     */
    public function testAuthenticateWithWrongPassword()
    {
        $this->setAuthenticationCredentials();
        $this->setAuthenticationUser();

        $this->options->expects($this->once())
            ->method('getEnableUserState')
            ->will($this->returnValue(false));

        // Set lowest possible to spent the least amount of resources/time
        $this->options->expects($this->once())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));

        $this->authEvent->expects($this->once())
            ->method('setCode')
            ->with(\Zend\Authentication\Result::FAILURE_CREDENTIAL_INVALID)
            ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once(1))
            ->method('setMessages')
            ->with(array('Supplied credential is invalid.'));

        $this->db->setMapper($this->mapper);
        $this->db->setOptions($this->options);

        $result = $this->db->authenticate($this->authEvent);

        $this->assertFalse($result);
        $this->assertFalse($this->db->isSatisfied());
    }

    /**
     * @covers LdcZfcUserOAuth2\Authentication\Adapter\Db::Authenticate
     */
    public function testAuthenticationAuthenticatesWithEmail()
    {
        $this->setAuthenticationCredentials('zfc-user@zf-commons.io');
        $this->setAuthenticationEmail();

        $this->options->expects($this->once())
            ->method('getEnableUserState')
            ->will($this->returnValue(false));

        $this->options->expects($this->once())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));

        $this->user->expects($this->exactly(2))
            ->method('getPassword')
            ->will($this->returnValue('$2a$04$5kq1mnYWbww8X.rIj7eOVOHXtvGw/peefjIcm0lDGxRTEjm9LnOae'));
        $this->user->expects($this->once())
                   ->method('getId')
                   ->will($this->returnValue(1));

        $this->storage->expects($this->any())
                      ->method('getNameSpace')
                      ->will($this->returnValue('test'));

        $this->authEvent->expects($this->once())
                        ->method('setIdentity')
                        ->with(1)
                        ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once())
                        ->method('setCode')
                        ->with(\Zend\Authentication\Result::SUCCESS)
                        ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once())
                        ->method('setMessages')
                        ->with(array('Authentication successful.'))
                        ->will($this->returnValue($this->authEvent));

        $this->db->setMapper($this->mapper);
        $this->db->setOptions($this->options);

        $result = $this->db->authenticate($this->authEvent);
    }

    /**
     * @covers LdcZfcUserOAuth2\Authentication\Adapter\Db::Authenticate
     */
    public function testAuthenticationAuthenticates()
    {
        $this->setAuthenticationCredentials();
        $this->setAuthenticationUser();

        $this->options->expects($this->once())
             ->method('getEnableUserState')
             ->will($this->returnValue(true));

        $this->options->expects($this->once())
             ->method('getAllowedLoginStates')
             ->will($this->returnValue(array(1, 2, 3)));

        $this->options->expects($this->once())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));

        $this->user->expects($this->exactly(2))
                   ->method('getPassword')
                   ->will($this->returnValue('$2a$04$5kq1mnYWbww8X.rIj7eOVOHXtvGw/peefjIcm0lDGxRTEjm9LnOae'));
        $this->user->expects($this->once())
                   ->method('getId')
                   ->will($this->returnValue(1));
        $this->user->expects($this->once())
                   ->method('getState')
                   ->will($this->returnValue(1));

        $this->storage->expects($this->any())
                      ->method('getNameSpace')
                      ->will($this->returnValue('test'));

        $this->authEvent->expects($this->once())
                        ->method('setIdentity')
                        ->with(1)
                        ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once())
                        ->method('setCode')
                        ->with(\Zend\Authentication\Result::SUCCESS)
                        ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once())
                        ->method('setMessages')
                        ->with(array('Authentication successful.'))
                        ->will($this->returnValue($this->authEvent));

        $this->db->setMapper($this->mapper);
        $this->db->setOptions($this->options);

        $result = $this->db->authenticate($this->authEvent);
    }

    protected function setAuthenticationEmail()
    {
        $this->mapper->expects($this->once())
            ->method('findByEmail')
            ->with('zfc-user@zf-commons.io')
            ->will($this->returnValue($this->user));

        $this->options->expects($this->once())
            ->method('getAuthIdentityFields')
            ->will($this->returnValue(array('email')));
    }

    protected function setAuthenticationUser()
    {
        $this->mapper->expects($this->once())
            ->method('findByUsername')
            ->with('ZfcUser')
            ->will($this->returnValue($this->user));

        $this->options->expects($this->once())
            ->method('getAuthIdentityFields')
            ->will($this->returnValue(array('username')));
    }

    protected function setAuthenticationCredentials($identity = 'ZfcUser', $credential = 'ZfcUserPassword')
    {
        $this->storage->expects($this->at(0))
            ->method('read')
            ->will($this->returnValue(array('is_satisfied' => false)));

        $post = $this->getMock('Zend\Stdlib\Parameters');
        $post->expects($this->at(0))
            ->method('get')
            ->with('identity')
            ->will($this->returnValue($identity));
        $post->expects($this->at(1))
            ->method('get')
            ->with('credential')
            ->will($this->returnValue($credential));

        $request = $this->getMock('Zend\Http\Request');
        $request->expects($this->exactly(2))
            ->method('getPost')
            ->will($this->returnValue($post));

        $this->authEvent->expects($this->exactly(2))
            ->method('getRequest')
            ->will($this->returnValue($request));
    }
}
