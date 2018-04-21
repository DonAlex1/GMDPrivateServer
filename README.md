## Geometry Dash Private Server
Basically a Geometry Dash Server Emulator.

Supported version of Geometry Dash: 2.113 (As of writing this [April 21, 2018]).

Required version of PHP: 5.4+

Need a mail to be used by the server to send emails for register accounts, change usernames, change passwords, activate accounts, etc...

### Setup
1) Upload the files on a webserver.
2) Edit [connection.php](https://github.com/DonAlex0/GMDPrivateServer/blob/master/config/connection.php) with the requeried information.
3) Edit [defaults.php](https://github.com/DonAlex0/GMDPrivateServer/blob/master/config/defaults.php) with the requeried information.
4) Edit [email.php](https://github.com/DonAlex0/GMDPrivateServer/blob/master/config/email.php) with the requeried information.
5) Edit [smtp.php](https://github.com/DonAlex0/GMDPrivateServer/blob/master/accounts/Mail/Mail/Mail/smtp.php), lines 125 and 132, with the requeried smtp server information.
6) Import [database.sql](https://github.com/DonAlex0/GMDPrivateServer/blob/master/database.sql) into a MySQL/MariaDB database.
7) Edit the links in GeometryDash.exe (some are Base64 encoded since 2.1).

### Credits
* Private messaging system by someguy28.

* Base for account settings by someguy28.

* Using [this](https://github.com/sathoro/php-xor-cipher) for XOR encryption - [XORCipher.php](incl/lib/XORCipher.php).

* Using [this](https://github.com/defuse/php-encryption) for cloud save encryption - [defuse-crypto.phar](incl/lib/defuse-crypto.phar).

* Most of the stuff in [generateHash.php](https://github.com/DonAlex0/GMDPrivateServer/blob/master/incl/lib/generateHash.php) has been figured out by pavlukivan and Italian APK Downloader.

* Based on [Cvolton's one](https://github.com/Cvolton/GMDprivateServer).