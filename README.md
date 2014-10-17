# LdcZfcUserApigility

## What?

ZfcUser <--> Apigility bridge to use ZfcUser accounts with Apigility's OAuth2 server.  

__WARNING__: This code is not yet tested, documented or been used in a live environment.  Approach with extreme caution.

## How?

1. Install module using Composer

   ```
   composer install adamlundrigan/ldc-zfc-user-apigility:dev-master@dev
   ```

2. Enable required modules in your `application.config.php` file:

   - ZfcBase
   - ZfcUser
   - LdcZfcUserApigility

3. Configure ZfcUser

4. Override the `zf-ouath2` configuration to use the provided storage provider:

   ```
    return array(
       'zf-oauth2' => array(
           'storage' => 'ldc-zfc-user-apigility-storage-pdo', 
       ),
   );
   ```
