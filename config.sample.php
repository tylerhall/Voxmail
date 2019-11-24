<?PHP
/*
	Note: This has only really been tested on an Ubuntu 18.04 box so far.
	You'll need at least PHP 7.2 as well as Composer installed for the dependencies...
	
	https://getcomposer.org

	And then the PHP IMAP extension...

	sudo apt install php7.2-imap

	composer require php-imap/php-imap
	composer require "swiftmailer/swiftmailer:^6.0"
*/

	require_once __DIR__ . '/vendor/autoload.php';
	require_once __DIR__ . '/includes/functions.php';
	require_once __DIR__ . '/includes/class.account.php';
	require_once __DIR__ . '/includes/class.email.php';
	$GlobalAccounts = array();
	
	// ##### SETTINGS GO HERE #####
	
	define('BASE_URL', 'https://domain.com/path/to/project/');
	define('SECRET', 'abc123OMGWTFBBQ'); // Some secret string that you can enter into the Shortcut to stop unauthorized users.

	// Duplciate this account block for each of the email accounts you want to use...
	$a = new Account();
	$a->siriName = 'Fastmail';
	$a->email = 'you@email.com';
	$a->fromName = 'Ron Swanson';
	$a->IMAPServer = 'imap.fastmail.com:993';
	$a->SMTPServer = 'smtp.fastmail.com:465';
	$a->username = 'user@fastmail.com';
	$a->password = '1234567890';
	$GlobalAccounts[] = $a;

	// $a = new Account();
	// $a->siriName = 'Gmail';
	// $a->email= 'somebody@gmail.com';
	// $a->fromName = 'Tyler Hall';
	// $a->IMAPServer = 'imap.gmail.com:993';
	// $a->SMTPServer = 'smtp.gmail.com:465';
	// $a->username = 'somebody@gmail.com';
	// $a->password = 'abcefghijkl';
	// $GlobalAccounts[] = $a;
