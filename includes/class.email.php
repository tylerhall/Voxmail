<?PHP
	class Email
	{
		public $id;
		public $message;
		public $siriName; // This is a dumb hack to let us handle the caching stuff

		public function archiveURL()
		{
			$account = urlencode($this->siriName);
			return BASE_URL . "?action=archive&account=$account&id={$this->id}";
		}

		public function deleteURL()
		{
			$account = urlencode($this->siriName);
			return BASE_URL . "?action=delete&account=$account&id={$this->id}";
		}

		public function spamURL()
		{
			$account = urlencode($this->siriName);
			return BASE_URL . "?action=spam&account=$account&id={$this->id}";
		}

		public function markAsReadURL()
		{
			$account = urlencode($this->siriName);
			return BASE_URL . "?action=markAsRead&account=$account&id={$this->id}";
		}
		
		public function markAsUnreadURL()
		{
			$account = urlencode($this->siriName);
			return BASE_URL . "?action=markAsUnread&account=$account&id={$this->id}";
		}

		public function replyURL()
		{
			$account = urlencode($this->siriName);
			return BASE_URL . "?action=reply&account=$account&id={$this->id}";
		}

		public function readURL()
		{
			$account = urlencode($this->siriName);
			return BASE_URL . "?action=read&account=$account&id={$this->id}";
		}
		
		public function summary()
		{
			$from = isset($this->message->fromName) ? $this->message->fromName : $this->message->fromAddress;
			$date_str = time2str(strtotime($this->message->date)) . ": ";
			$an_str = in_array(strtolower(substr($this->siriName, 0, 1)), array('a', 'e', 'i', 'o', 'u')) ? 'an' : 'a';
			return $date_str . "$an_str {$this->siriName} email from $from titled {$this->message->subject}.";
		}
		
		public function object()
		{
			$obj = array();
			$obj['id'] = $this->id;
			$obj['siriName'] = $this->siriName;
			$obj['archiveURL'] = $this->archiveURL();
			$obj['deleteURL'] = $this->deleteURL();
			$obj['spamURL'] = $this->spamURL();
			$obj['markAsReadURL'] = $this->markAsReadURL();
			$obj['markAsUnreadURL'] = $this->markAsUnreadURL();
			$obj['replyURL'] = $this->replyURL();
			$obj['readURL'] = $this->readURL();
			$obj['fromEmail'] = $this->message->fromAddress;
			$obj['fromName'] = $this->message->fromName;
			$obj['subject'] = $this->message->subject;
			$obj['ts'] = strtotime($this->message->date);
			$obj['summary'] = $this->summary();
			return $obj;
		}

		public static function sortByDateDescending($a, $b)
		{
			$a_ts = strtotime($a->message->date);
			$b_ts = strtotime($b->message->date);
		
			if($a_ts == $b_ts) {
				return 0;
			}

			return ($a_ts <  $b_ts) ? 1 : -1;	
		}
	}
