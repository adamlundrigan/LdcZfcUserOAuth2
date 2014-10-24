# LdcZfcUserOAuth2

## What?

An extension for [`zf-oauth2`](https://github.com/zfcampus/zf-oauth2) allowing use of ZfcUser as authentication source

__WARNING__: This code is not yet tested, documented or been used in a live environment.  Approach with extreme caution.

## How?

1. Install module using Composer

   ```
   composer install adamlundrigan/ldc-zfc-user-oauth2:<version>
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

## TODO

 - [x] Use ZfcUser's authentication mechanism in OAuth2 server
 - [x] Populate ZfcUser auth storage when OAuth2 server authentication succeeds 
 - [ ] Some tests might be a good idea
 - [ ] Some documentation and an example might also be good ideas
