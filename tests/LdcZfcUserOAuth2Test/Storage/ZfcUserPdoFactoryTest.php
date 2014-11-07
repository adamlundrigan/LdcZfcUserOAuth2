<?php
namespace LdcZfcUserOAuth2Test\Storage;

use LdcZfcUserOAuth2Test\TestCase;
use LdcZfcUserOAuth2\Storage\ZfcUserPdoFactory;

class ZfcUserPdoFactoryTest extends TestCase
{
    public function setUp()
    {
        $this->serviceManager = $this->getServiceManager();
        $this->serviceManager->setAllowOverride(true);
        $this->serviceManager->setService('Config', []);
    }

    public function testCreateServiceThrowsExceptionIfConfigurationIsMissing()
    {
        $this->setExpectedException('ZF\OAuth2\Adapter\Exception\RuntimeException');

        $factory = new ZfcUserPdoFactory();
        $obj = $factory->createService($this->serviceManager);
    }

    public function testCreateServiceDependsOnStorageBridgeService()
    {
        $this->serviceManager->setService('Config', [
            'zf-oauth2' => [
                'db' => [
                    'dsn' => '',
                    'username' => '',
                    'password' => '',
                    'options' => array(),
                ],
            ],
        ]);

        $this->setExpectedExceptionRegExp(
            'Zend\ServiceManager\Exception\ServiceNotFoundException',
            '{ldc-zfc-user-oauth2-storage-bridge}is'
        );

        $factory = new ZfcUserPdoFactory();
        $obj = $factory->createService($this->serviceManager);
    }

    public function testCreateServiceWillInjectCustomStorageSettings()
    {
        $this->serviceManager->setService('Config', [
            'zf-oauth2' => [
                'db' => [
                    'dsn' => 'sqlite::memory:',
                    'username' => '',
                    'password' => '',
                    'options' => array(),
                ],
                'storage_settings' => [
                    'client_table' => 'foobar',
                ],
            ],
        ]);
        $this->serviceManager->setService(
            'ldc-zfc-user-oauth2-storage-bridge',
            \Mockery::mock('LdcZfcUserOAuth2\Storage\ZfcUserStorageBridge')
        );

        $factory = new ZfcUserPdoFactory();
        $obj = $factory->createService($this->serviceManager);

        $expected = array(
            'client_table' => 'foobar',
            'access_token_table' => 'oauth_access_tokens',
            'refresh_token_table' => 'oauth_refresh_tokens',
            'code_table' => 'oauth_authorization_codes',
            'user_table' => 'oauth_users',
            'jwt_table'  => 'oauth_jwt',
            'scope_table'  => 'oauth_scopes',
            'public_key_table'  => 'oauth_public_keys',
        );
        $this->assertAttributeEquals($expected, 'config', $obj);
    }

    public function testCreateServiceHappyCase()
    {
        $this->serviceManager->setService('Config', [
            'zf-oauth2' => [
                'db' => [
                    'dsn' => 'sqlite::memory:',
                    'username' => '',
                    'password' => '',
                    'options' => array(),
                ],
            ],
        ]);
        $this->serviceManager->setService('ldc-zfc-user-oauth2-storage-bridge', \Mockery::mock('LdcZfcUserOAuth2\Storage\ZfcUserStorageBridge'));

        $factory = new ZfcUserPdoFactory();
        $obj = $factory->createService($this->serviceManager);

        $this->assertInstanceOf('LdcZfcUserOAuth2\Storage\ZfcUserPdo', $obj);
    }
}
