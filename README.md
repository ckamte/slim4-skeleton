## Requirements
1. PHP version 8.2 or above
2. Apache Web-server with URL rewrite
   ### *Config example*
   ```
   <Directory "${SRVROOT}/htdocs">
	   AllowOverride All
   </Directory>

   LoadModule rewrite_module modules/mod_rewrite.so
   ```

## How to use
1. Clone or download as zip and extract to your server's document root
2. Change .env.default file name to .env
3. Edit .env file
4. Download required packages using composer
   ```
   composer install
   ```
