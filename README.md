# WebAuthn

Project : BaTh_IHEG1-2-17_fs_mt

# General Information
this is a prototyp to extend an existing web application with the possiblity to login with 3 policys:
'Password only','2-Factor-Authentication' and 'Passwordless'

this work extends form the original source file from microsoft webAuth code snippets and is optimised for a specific demonstration platform we used. Original source: https://github.com/adrianba/fido-snippets

with changes to utility.php and a xampp server, you should be able to create a local test environment. 
Do NOT use the unchanged code in a productive environment, no security checks were done! This is a proof of concept.
we assume no liability or responsibility if the project does not behave like you thought it would.


## PHP Version
Our test-installations were running PHP 5.6 and a PHP 5.2 versions. Other versions were not tested by the project team.


## Installation

### Files
Copy all the files on your webserver in the root directory e.g. /var/www
If you want to use the webflow version, copy all the files additionally into /KrediWF/Model/version/Addon/ of your webflow to extend it.
To use the webflow administration version, you need to install the template "webflow - Erweiterungen - WindowsHello".


### MSSQL or MYSQL
Execute the .sql scripts in /SQL/ folder on your database. upgradeThesisDb.sql is MySQL, upgradeThesisDbMSSql.sql is for MSSQL database.


### PHP.ini
the assertion is build using phpseclib\Crypt classes.
php.ini must be extended to use phpseclib\Crypt. Search for the include_path=  in your php.ini file and add our string

include_path=".;C:\xampp\php\pear\;C:\xampp\htdocs\WebAuthn;C:\xampp\htdocs\WebAuthn\phpseclib;C:\xampp\htdocs\WebAuthn\phpseclib\Crypt"

for our prototype on a linux server, we used the following include_path (could vary on your system):
include_path = ".:/usr/local/zend/share/ZendFramework/library:/usr/local/zend/share/pear:/var/www/WebAuthn-master:/var/www/WebAuthn-master/phpseclib:/var/www/WebAuthn-master/phpseclib/Crypt"


### Configuration
Check the values in /PHP/utility.php of this project and set in _plugin_utility::getConfiguration(XXXX); the right configuration for your installation


### Installation Tests
open this url in a browser to test the installation:
<IP OR localhost>/WebAuthn-master/PHP/test.php


### Start the example
Open the authentication flow by opening the url 
<IP OR localhost>/WebAuthn-master/PHP/index.php
or
<IP OR localhost>/WebAuthn-master/PHP/welcome.php

And try login in with one of these users:  tscm   ,   schf    or   hello
