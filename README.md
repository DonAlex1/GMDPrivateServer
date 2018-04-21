## Geometry Dash Private Server
Basically a Geometry Dash Server Emulator.

Supported version of Geometry Dash: 2.113 (As of writing this [April 21, 2018]).

Required version of PHP: 5.4+

Need a mail to be used by the server to send emails for register accounts, change usernames, change passwords, activate accounts, etc...

### Setup
1) Upload the files on a webserver.
2) Edit connection.php (config/connection.php) with the requeried information.
3) Edit defaults.php (config/defaults.php) with the requeried information.
4) Edit email.php (config/email.php) with the requeried information.
5) Edit smtp.php (accounts/Mail/Mail/Mail/smtp.php), lines 125 and 132, with the requeried smtp server information.
6) Import database.sql into a MySQL/MariaDB database.
7) Edit the links in GeometryDash.exe (some are Base64 encoded since 2.1).

### Credits
Private Messaging system by someguy28.

Base for account settings by someguy28.

Using this for XOR encryption - https://github.com/sathoro/php-xor-cipher - (incl/lib/XORCipher.php).

Using this for cloud save encryption - https://github.com/defuse/php-encryption - (incl/lib/defuse-crypto.phar).

Most of the stuff in generateHash.php has been figured out by pavlukivan and Italian APK Downloader, so credits to them.

Based on Cvolton's one - https://github.com/Cvolton/GMDprivateServer