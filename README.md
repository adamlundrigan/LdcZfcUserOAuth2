# LdcZfcUserOAuth2

## What?

An extension for [`zf-oauth2`](https://github.com/zfcampus/zf-oauth2) allowing use of ZfcUser as authentication source

----

[![Latest Stable Version](https://poser.pugx.org/adamlundrigan/ldc-zfc-user-oauth2/v/stable.svg)](https://packagist.org/packages/adamlundrigan/ldc-zfc-user-oauth2) [![License](https://poser.pugx.org/adamlundrigan/ldc-zfc-user-oauth2/license.svg)](https://packagist.org/packages/adamlundrigan/ldc-zfc-user-oauth2) [![Build Status](https://travis-ci.org/adamlundrigan/LdcZfcUserOAuth2.svg?branch=master)](https://travis-ci.org/adamlundrigan/LdcZfcUserOAuth2) [![Code Coverage](https://scrutinizer-ci.com/g/adamlundrigan/LdcZfcUserOAuth2/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/adamlundrigan/LdcZfcUserOAuth2/?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/adamlundrigan/LdcZfcUserOAuth2/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/adamlundrigan/LdcZfcUserOAuth2/?branch=master)

----

## How?

1. Install module using Composer

   ```
   composer require adamlundrigan/ldc-zfc-user-oauth2:<version>
   ```

2. Enable required modules in your `application.config.php` file:

   - ZfcBase
   - ZfcUser
   - LdcZfcUserOAuth2

3. Configure ZfcUser

4. Override the `zf-ouath2` configuration to use the provided storage provider:

   ```
    return array(
       'zf-oauth2' => array(
           'storage' => 'ldc-zfc-user-oauth2-storage-pdo', 
       ),
   );
   ```

5. Override the authentication adapter used by ZfcUser.  Locate the `auth_adapters` key in your `zfc-user.global.php` config file and replace it with this:

   ```
   'auth_adapters' => array( 100 => 'ldc-zfc-user-oauth2-authentication-adapter-db' ),
   ```

## TODO

 - [x] Use ZfcUser's authentication mechanism in OAuth2 server
 - [x] Populate ZfcUser auth storage when OAuth2 server authentication succeeds 
 - [ ] Some tests might be a good idea
 - [ ] Some documentation and an example might also be good ideas
