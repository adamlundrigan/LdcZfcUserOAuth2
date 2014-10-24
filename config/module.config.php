<?php
return array(
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'service_manager' => array(
        'invokables' => array(
            'ldc-zfc-user-oauth2-authentication-adapter-db' => 'LdcZfcUserOAuth2\Authentication\Adapter\Db',
        ),
        'factories' => array(
            'ldc-zfc-user-oauth2-storage-pdo' => 'LdcZfcUserOAuth2\Storage\ZfcUserPdoFactory',
            'ldc-zfc-user-oauth2-storage-bridge' => 'LdcZfcUserOAuth2\Storage\ZfcUserStorageBridgeFactory',
        ),
    ),
);
