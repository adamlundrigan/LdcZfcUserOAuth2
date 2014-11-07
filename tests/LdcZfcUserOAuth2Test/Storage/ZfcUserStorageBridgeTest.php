<?php
namespace LdcZfcUserOAuth2Test\Storage;

use LdcZfcUserOAuth2Test\TestCase;
use LdcZfcUserOAuth2\Storage\ZfcUserStorageBridge;
use Zend\Authentication\Result as AuthResult;

class ZfcUserStorageBridgeTest extends TestCase
{
    public function setUp()
    {
        $this->userMapper = \Mockery::mock('ZfcUser\Mapper\UserInterface');
        $this->authService = \Mockery::mock('Zend\Authentication\AuthenticationServiceInterface');
        $this->authAdapter = \Mockery::mock('ZfcUser\Authentication\Adapter\AdapterChain');
        $this->authOptions = \Mockery::mock('ZfcUser\Options\AuthenticationOptionsInterface');

        $this->mockUserData = ['user_id' => 42, 'email' => 'foo@bar.com', 'username' => 'foo', 'display_name' => '', 'state' => null];
        $this->userHydrator = new \ZfcUser\Mapper\UserHydrator();
        $this->mockUserObject = new \ZfcUser\Entity\User();
        $this->userHydrator->hydrate($this->mockUserData, $this->mockUserObject);

        $this->service = new ZfcUserStorageBridge(
            $this->userMapper,
            $this->authService,
            $this->authAdapter,
            $this->authOptions
        );
    }

    public function testCheckUserCredentialsHappyCaseAuthSuccess()
    {
        $this->authAdapter->shouldIgnoreMissing();

        $authResult = new AuthResult(AuthResult::SUCCESS, []);
        $this->authService->shouldReceive('authenticate')
                          ->once()
                          ->with($this->authAdapter)
                          ->andReturn($authResult);

        $this->assertTrue($this->service->checkUserCredentials('foo', 'bar'));
    }

    public function testCheckUserCredentialsShortCircuitsIfAuthPrepFails()
    {
        $this->authAdapter->shouldReceive('prepareForAuthentication')
                          ->once()
                          ->andReturn(new \Zend\Stdlib\Response());

        $this->authService->shouldReceive('authenticate')
                          ->never();

        $this->assertFalse($this->service->checkUserCredentials('foo', 'bar'));
    }

    public function testCheckUserCredentialsHappyCaseAuthFailure()
    {
        $this->authAdapter->shouldIgnoreMissing();

        $authResult = new AuthResult(AuthResult::FAILURE_UNCATEGORIZED, []);
        $this->authService->shouldReceive('authenticate')
                          ->once()
                          ->with($this->authAdapter)
                          ->andReturn($authResult);

        $this->assertFalse($this->service->checkUserCredentials('foo', 'bar'));
    }

    public function testGetUserDetailsEmailHappyCase()
    {
        $this->authOptions->shouldReceive('getAuthIdentityFields')
                          ->andReturn(['email']);

        $this->userMapper->shouldReceive('findByEmail')
                         ->with($this->mockUserData['email'])
                         ->once()
                         ->andReturn($this->mockUserObject);

        $result = $this->service->getUserDetails($this->mockUserData['email']);
        $this->verifyUserResult($result);
    }

    public function testGetUserDetailsUsernameHappyCase()
    {
        $this->authOptions->shouldReceive('getAuthIdentityFields')
                          ->andReturn(['username']);

        $this->userMapper->shouldReceive('findByUsername')
                         ->with($this->mockUserData['username'])
                         ->once()
                         ->andReturn($this->mockUserObject);

        $result = $this->service->getUserDetails($this->mockUserData['username']);
        $this->verifyUserResult($result);
    }

    public function testGetUserDetailsRepectsOrderingOfIdentitfyFieldsOnSecondFieldSuccess()
    {
        $this->authOptions->shouldReceive('getAuthIdentityFields')
                          ->andReturn(['username', 'email']);

        $this->userMapper->shouldReceive('findByUsername')
                         ->once()
                         ->andReturn(null);

        $this->userMapper->shouldReceive('findByEmail')
                         ->once()
                         ->andReturn($this->mockUserObject);

        $result = $this->service->getUserDetails($this->mockUserData['email']);
        $this->verifyUserResult($result);
    }

    public function testGetUserDetailsRepectsOrderingOfIdentitfyFieldsOnFirstFieldSuccess()
    {
        $this->authOptions->shouldReceive('getAuthIdentityFields')
                          ->andReturn(['username', 'email']);

        $this->userMapper->shouldReceive('findByUsername')
                         ->once()
                         ->andReturn($this->mockUserObject);

        $this->userMapper->shouldReceive('findByEmail')
                         ->never();

        $result = $this->service->getUserDetails($this->mockUserData['username']);
        $this->verifyUserResult($result);
    }

    public function testGetUserDetailsRepectsOrderingOfIdentitfyFieldsUserNotFound()
    {
        $this->authOptions->shouldReceive('getAuthIdentityFields')
                          ->andReturn(['username', 'email']);

        $this->userMapper->shouldReceive('findByUsername')
                         ->once()
                         ->andReturn(null);

        $this->userMapper->shouldReceive('findByEmail')
                         ->once()
                         ->andReturn(null);

        $result = $this->service->getUserDetails($this->mockUserData['username']);
        $this->assertNull($result);
    }

    protected function verifyUserResult($result)
    {
        $this->assertInternalType('array', $result);
        foreach ($this->mockUserData as $key => $value) {
            $this->assertArrayHasKey($key, $result);
            $this->assertEquals($value, $result[$key]);
        }
    }
}
