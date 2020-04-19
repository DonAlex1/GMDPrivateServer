## Geometry Dash Private Server
Basically a Geometry Dash Server Emulator.

Supported version of Geometry Dash: 2.113 (As of writing this [July 26, 2018]).

Required version of PHP: 5.4+

Required [PEAR](https://pear.php.net/).

### Setup
1) Upload the files on a webserver.
2) Edit [connection.php](config/connection.php) with the requeried information.
3) Edit [defaults.php](config/defaults.php) with the requeried information.
* If you know how to set up a **real** email to use with your server and you want to use one also follow this steps:
    * Edit [email.php](config/email.php) with the requeried information.
    * Edit [smtp.php](accounts/Mail/Mail/Mail/smtp.php) with the requeried smtp server information the lines 125 and 132.
    * Edit [changePassword.php](dashboard/account/changePassword.php) and add the email body at line 95.
    * Edit [changeUsername.php](dashboard/account/changeUsername.php) and add the email body at line 128.
    * Edit [lostPassword.php](dashboard/account/lostPassword.php) and add the email body at line 91.
    * Edit [registerGJAccount.php](accounts/registerGJAccount.php) and add the email body at line 29.
4) Import [database.sql](database.sql) into a MySQL/MariaDB database.
5) Edit the links in GeometryDash.exe (some are Base64 encoded since 2.1).

### Credits
* Private messaging system by someguy28.

* Base for account settings by someguy28.

* Using [this](https://github.com/sathoro/php-xor-cipher) for XOR encryption - [XORCipher.php](incl/lib/XORCipher.php).

* Most of the stuff in [generateHash.php](incl/lib/generateHash.php) has been figured out by pavlukivan and Italian APK Downloader.

* Based on [Cvolton's Private Server](https://github.com/Cvolton/GMDprivateServer).
