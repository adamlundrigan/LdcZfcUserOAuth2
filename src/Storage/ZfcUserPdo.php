<?php
namespace LdcZfcUserApigility\Storage;

use OAuth2\Storage\Pdo as OAuth2Pdo;
use LdcZfcUserApigility\Storage\ZfcUserStorageBridge;

class ZfcUserPdo extends OAuth2Pdo
{
    /**
     * @var ZfcUserStorageBridge
     */
    protected $bridge;
    
    public function __construct($connection, $config, ZfcUserStorageBridge $bridge) 
    {
        parent::__construct($connection, $config);
        $this->bridge = $bridge;
    }
    
    public function checkUserCredentials($username, $password)
    {
        return $this->bridge->checkUserCredentials($username, $password);
    }
    
    public function getUserDetails($username)
    {
        return $this->bridge->getUserDetails($username);
    }

}