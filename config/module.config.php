<?php
return array(
    'router' => array(
        'routes' => array(
            'zfcuser' => array(
                'child_routes' => array(
                    'login' => array(
                        'options' => array(
                            'defaults' => array(
                                'controller' => 'ZF\OAuth2\Controller\Auth',
                                'action'     => 'token',
                            ),
                        ),
                    ),
                    'authenticate' => array(
                        'options' => array(
                            'defaults' => array(
                                'controller' => 'ZF\OAuth2\Controller\Auth',
                                'action'     => 'token',
                            ),
                        ),
                    ),
                    'logout' => array(
                        'options' => array(
                            'defaults' => array(
                                'controller' => 'ldc-zfc-user-apigility-controller',
                            ),
                        ),
                    ),
                    'register' => array(
                        'options' => array(
                            'defaults' => array(
                                'controller' => 'ldc-zfc-user-apigility-controller',
                            ),
                        ),
                    ),
                    'changepassword' => array(
                        'options' => array(
                            'defaults' => array(
                                'controller' => 'ldc-zfc-user-apigility-controller',
                            ),
                        ),
                    ),
                    'changeemail' => array(
                        'options' => array(
                            'defaults' => array(
                                'controller' => 'ldc-zfc-user-apigility-controller',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    
    'controllers' => array(
        'invokables' => array(
            'ldc-zfc-user-apigility-controller' => 'LdcZfcUserApigility\Controller\UserController',
        ),
    ),
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'service_manager' => array(
        'invokables' => array(
            'ldc-zfc-user-apigility-authentication-adapter-db' => 'LdcZfcUserApigility\Authentication\Adapter\Db',
        ),
        'factories' => array(
            'ldc-zfc-user-apigility-storage-pdo' => 'LdcZfcUserApigility\Storage\ZfcUserPdoFactory',
            'ldc-zfc-user-apigility-storage-bridge' => 'LdcZfcUserApigility\Storage\ZfcUserStorageBridgeFactory',
        ),
    ),
);
