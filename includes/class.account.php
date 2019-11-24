<?PHP
	use PhpImap\Mailbox;
	use PhpImap\Exceptions\ConnectionException;

	class Account
	{
		public $siriName;
		public $email;
		public $fromName = '';
		public $IMAPServer;
		public $IMAPUsername;
		public $IMAPPassword;
		public $SMTPServer;
		public $SMTPUsername;
		public $SMTPPassword;
		
		public $inboxFolder = 'INBOX';
		public $archiveFolder = 'Archive';
		public $trashFolder = 'Trash';
		public $spamFolder = 'Junk Mail';

	    public function __set($property, $value)
		{
			if(property_exists($this, $property)) {
				$this->$property = $value;
			}

			if($property == "server") {
				$this->IMAPServer = $value;
				$this->SMTPServer = $value;
			}

			if($property == "username") {
				$this->IMAPUsername = $value;
				$this->SMTPUsername = $value;
			}

			if($property == "password") {
				$this->IMAPPassword = $value;
				$this->SMTPPassword = $value;
			}
	    }

		public function __get($property)
		{
			if($property === 'mailbox') {
				return new Mailbox('{' . $this->IMAPServer . '/imap/ssl}', $this->IMAPUsername, $this->IMAPPassword);
			}

			if($property === 'inbox') {
				return new Mailbox('{' . $this->IMAPServer . '/imap/ssl}' . $this->inboxFolder, $this->IMAPUsername, $this->IMAPPassword);
			}
			
			if($property === 'archiveMailbox') {
				return new Mailbox('{' . $this->IMAPServer . '/imap/ssl}' . $this->archiveFolder, $this->IMAPUsername, $this->IMAPPassword);
			}
			
			if($property === 'trashMailbox') {
				return new Mailbox('{' . $this->IMAPServer . '/imap/ssl}' . $this->trashFolder, $this->IMAPUsername, $this->IMAPPassword);
			}
			
			if($property === 'spamMailbox') {
				return new Mailbox('{' . $this->IMAPServer . '/imap/ssl}' . $this->spamFolder, $this->IMAPUsername, $this->IMAPPassword);
			}

			return $this->$property;
		}

		public function salt()
		{
			$salt = '';
			foreach(get_object_vars($this) as $k => $v) {
				if(gettype($this->$k) === 'string') {
					$salt .= $v;
				}
			}
			return sha1($salt);
		}
		
		public function SMTPTransport()
		{
			list($server, $port) = explode(':', $this->SMTPServer);
			$transport = (new Swift_SmtpTransport($server, $port, 'ssl'))
			  ->setUsername($this->SMTPUsername)
			  ->setPassword($this->SMTPPassword);
			return $transport;
		}

		public function getEmailIDs($query = 'UNSEEN', $box = NULL)
		{
			if(!isset($box)) {
				$box = $this->inbox;
			}

		    try {
		        return $box->searchMailbox($query);
		    } catch(ConnectionException $ex) {
		        die("IMAP connection failed: " . $ex->getMessage());
		    } catch (Exception $ex) {
		        die("An error occured: " . $ex->getMessage());
		    }
		}

		function getEmails($query = 'UNSEEN', $box = NULL) {
			if(!isset($box)) {
				$box = $this->inbox;
			}

			$ids = $this->getEmailIDs($query, $box);

			$emails = array();
		    foreach($ids as $id) {
				$email = new Email();
				$email->id = $id;
		        $email->message = $box->getMail($id, false);
				$email->siriName = $this->siriName;
				$emails[] = $email;
			}

			return $emails;
		}

		public static function findAccounts($accounts_to_search, $query = 'all')
		{
			if(strtolower(trim($query)) === 'all') {
				return $accounts_to_search;
			}
			
			if(count($accounts_to_search) == 1) {
				return $accounts_to_search;
			}

			// Return an indexed account if $query is a number...
			if(is_numeric($query)) {
				return $accounts_to_search[intval($query)];
			}
			
			// Search by SiriName...
			foreach($accounts_to_search as $a) {
				if(strtolower(trim($query)) == strtolower(trim($a->siriName))) {
					return array($a);
				}
			}
			
			// Fallback to searching by soundex...
			foreach($accounts_to_search as $a) {
				if(soundex(trim($query)) == soundex(trim($a->siriName))) {
					return array($a);
				}
			}

			return array();
		}
	}
