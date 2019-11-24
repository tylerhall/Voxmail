<?PHP
	require 'config.php';

	if(!isset($_SERVER['HTTP_SECRET']) || ($_SERVER['HTTP_SECRET'] !== SECRET)) {
		die();
	}

	// Calculate the global salt...
	$str = '';
	foreach($GlobalAccounts as $a) {
		$str .= sha1($a->salt());
	}
	define('SIRI_MAIL_SALT', sha1($str));

	// Do POST and header security check

	$action = strtolower(isset($_REQUEST['action']) ? $_REQUEST['action'] : 'summarizeUnread');

	$account_query = isset($_REQUEST['account']) ? $_REQUEST['account'] : 'all';
	$accounts = Account::findAccounts($GlobalAccounts, $account_query);
	if(count($accounts) == 0) {
		$accounts = $GlobalAccounts;
	}

	// $a = $accounts[0];
	// $boxes = $a->mailbox->getMailboxes();
	// print_r($boxes);
	// exit;

	if($action === 'summarizeunread') {
		$results = summarizeUnread($accounts);
		echo json_encode($results);
		exit;
	}

	if($action === 'getunread') {
		$results = getUnread($accounts);
		header('Content-Type: application/json');
		echo json_encode($results);
		exit;
	}

	if($action === 'read') {
		if(count($accounts) != 1) {
			die('Could not find correct email account.');
		}
		if(!isset($_REQUEST['id'])) {
			die('Please specify a message to read.');
		}
		$results = readMessage($accounts[0], $_REQUEST['id']);
		echo $results['summary'];
		exit;
	}

	if($action == 'markasread') {
		if(count($accounts) != 1) {
			die('Could not find correct email account.');
		}
		if(!isset($_REQUEST['id'])) {
			die('Please specify a message to mark as read.');
		}
		$results = markEmailAsRead($accounts[0], $_REQUEST['id']);
		echo $results['summary'];
		exit;
	}

	if($action == 'markasunread') {
		if(count($accounts) != 1) {
			die('Could not find correct email account.');
		}
		if(!isset($_REQUEST['id'])) {
			die('Please specify a messageto mark as read.');
		}
		$results = markEmailAsUnread($accounts[0], $_REQUEST['id']);
		echo $results['summary'];
		exit;
	}

	if($action == 'archive') {
		if(count($accounts) != 1) {
			die('Could not find correct email account.');
		}
		if(!isset($_REQUEST['id'])) {
			die('Please specify a message to archive.');
		}
		$results = archiveEmail($accounts[0], $_REQUEST['id']);
		echo $results['summary'];
		exit;
	}

	if($action == 'delete') {
		if(count($accounts) != 1) {
			die('Could not find correct email account.');
		}
		if(!isset($_REQUEST['id'])) {
			die('Please specify a message to delete.');
		}
		$results = deleteEmail($accounts[0], $_REQUEST['id']);
		echo $results['summary'];
		exit;
	}

	if($action == 'spam') {
		if(count($accounts) != 1) {
			die('Could not find correct email account.');
		}
		if(!isset($_REQUEST['id'])) {
			die('Please specify a message to mark as spam.');
		}
		$results = spamEmail($accounts[0], $_REQUEST['id']);
		echo $results['summary'];
		exit;
	}

	if($action == 'reply') {
		if(count($accounts) != 1) {
			die('Could not find correct email account.');
		}
		if(!isset($_REQUEST['id'])) {
			die('Please specify a message to reply to.');
		}		
		if(!isset($_REQUEST['body'])) {
			die('You did not supply any text to send.');
		}
		if(strlen(trim($_REQUEST['body'])) == 0) {
			die('Your reply is empty. Not sending anything.');
		}
		$results = replyToEmail($accounts[0], $_REQUEST['id'], $_REQUEST['body']);
		echo $results['summary'];
		exit;
	}
