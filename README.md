# WebAuthn

Project : BaTh_IHEG1-2-17_fs_mt

# General Information

## PHP Version > 5.2
Our Testinstallations were a PHP 5.6 and a PHP 5.2 installation. Other versions were not tested by the project team.


## Installation

php.ini must be extended:
include_path=".;C:\xampp\php\pear\;C:\xampp\htdocs\WebAuthn;C:\xampp\htdocs\WebAuthn\phpseclib;C:\xampp\htdocs\WebAuthn\phpseclib\Crypt"

for our prototype on a Linux server, we used the following include_path:
include_path = ".:/usr/local/zend/share/ZendFramework/library:/usr/local/zend/share/pear:/var/www/WebAuthn-master:/var/www/WebAuthn-master/phpseclib:/var/www/WebAuthn-master/phpseclib/Crypt"




