<?php
namespace LdcZfcUserApigility\Storage;

use OAuth2\Storage\UserCredentialsInterface;
use ZfcUser\Mapper\UserInterface as UserMapperInterface;
use ZfcUser\Entity\UserInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use ZfcUser\Authentication\Adapter\AdapterChain as AuthenticationAdapterChain;
use ZfcUser\Options\AuthenticationOptionsInterface;

class ZfcUserStorageBridge implements UserCredentialsInterface
{
   
    /**
     * ZfcUser mapper
     * 
     * @var \ZfcUser\Mapper\UserInterface
     */
    protected $mapper;
    
    /**
     * Authentication Service

     * @var \Zend\Authentication\AuthenticationServiceInterface
     */
    protected $auth;
    
    /**
     * Authentication Adapter
     * 
     * @var \ZfcUser\Authentication\Adapter\AdapterChain
     */
    protected $authAdapter;

    /**
     * ZfcUser Module Options
     * 
     * @var \ZfcUser\Options\AuthenticationOptionsInterface;
     */
    protected $authOptions;
    
    public function __construct(UserMapperInterface $mapper, AuthenticationServiceInterface $auth, AuthenticationAdapterChain $adapter, AuthenticationOptionsInterface $options)
    {
        $this->mapper = $mapper;
        $this->auth = $auth;
        $this->authAdapter = $adapter;
        $this->authOptions = $options;
    }
            
    /**
     * Delegate checking of user credentials to ZfcUser's onboard adapter chain
     * 
     * @param string $username
     * @param string $password
     * @return boolean
     */
    public function checkUserCredentials($username, $password)
    {
        $request = new \Zend\Http\Request();
        $request->getPost()->set('identity', $username);
        $request->getPost()->set('credential', $password);
        
        $adapterResult = $this->authAdapter->prepareForAuthentication($request);
        if ( $adapterResult instanceof \Zend\Stdlib\ResponseInterface ) {
            return false;
        }
        
        $authResult = $this->auth->authenticate($this->authAdapter);
        if ( ! $authResult->isValid() ) {
            $this->authAdapter->resetAdapters();
            return false;
        }
        return true;
    }

    /**
     * Load user details based on configured authentication fields
     * 
     * @param string $username
     * @return array|null
     */
    public function getUserDetails($username)
    {
        $user = null;
        $fields = $this->authOptions->getAuthIdentityFields();
        
        while (!is_object($user) && count($fields) > 0) {
            $mode = array_shift($fields);
            switch ($mode) {
                case 'username':
                    $user = $this->mapper->findByUsername($username);
                    break;
                case 'email':
                    $user = $this->mapper->findByEmail($username);
                    break;
            }
        }
        if ( ! $user instanceof UserInterface) {
            return NULL;
        }
        
        return array(
            'user_id'       => $user->getId(),
            'username'      => $user->getUsername(),
            'email'         => $user->getEmail(),
            'display_name'  => $user->getDisplayName(),
            'state'         => $user->getState(),
        );
    }
    
}

