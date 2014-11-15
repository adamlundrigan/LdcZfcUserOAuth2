<?php
namespace LdcZfcUserOAuth2\Storage;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZF\OAuth2\Adapter\Exception;

class ZfcUserPdoFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (!isset($config['zf-oauth2']['db']) || empty($config['zf-oauth2']['db'])) {
            throw new Exception\RuntimeException(
                'The database configuration [\'zf-oauth2\'][\'db\'] for OAuth2 is missing'
            );
        }

        $username = isset($config['zf-oauth2']['db']['username']) ? $config['zf-oauth2']['db']['username'] : null;
        $password = isset($config['zf-oauth2']['db']['password']) ? $config['zf-oauth2']['db']['password'] : null;
        $options  = isset($config['zf-oauth2']['db']['options']) ? $config['zf-oauth2']['db']['options'] : array();

        $oauth2ServerConfig = array();
        if (isset($config['zf-oauth2']['storage_settings']) && is_array($config['zf-oauth2']['storage_settings'])) {
            $oauth2ServerConfig = $config['zf-oauth2']['storage_settings'];
        }

        $obj = new ZfcUserPdo(
            array(
                'dsn'      => $config['zf-oauth2']['db']['dsn'],
                'username' => $username,
                'password' => $password,
                'options'  => $options,
            ),
            $oauth2ServerConfig,
            $serviceLocator->get('ldc-zfc-user-oauth2-storage-bridge')
        );

        return $obj;
    }
}
