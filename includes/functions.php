<?PHP
	// Returns a summary of the number of unread emails in each account.
	function summarizeUnread($accounts) {
		$summary = '';
		foreach($accounts as $a) {
			$ids = $a->getEmailIDs('UNSEEN');

			if(count($ids) == 0) {
				$summary .= $a->siriName . " has no unread messages.\n";
			} else {
				$s = (count($ids) == 1) ? '' : 's';
				$summary .= $a->siriName . ' has ' . count($ids) . ' unread message' . $s . ".\n";
			}
		}

		return array('summary' => $summary);
	}

	// Returns the sender and subjects of all unread emails sorted by newest to oldest.
	function getUnread($accounts)
	{
		$all_emails = array();
		foreach($accounts as $a) {
			$emails = $a->getEmails('UNSEEN');
			$all_emails = array_merge($all_emails, $emails);
		}

		$results = array();
		$results['count'] = count($all_emails);

		if($results['count'] == 0) {
			$summary = 'You have no unread emails.';
			$long_summary = '';
		} else if($results['count'] == 1) {
			$count = count($accounts);
			if($count == 1) {
				$summary = "You have one unread {$accounts[0]->siriName} email.\n";
				$long_summary = "You have one unread {$accounts[0]->siriName} email. Here it is.\n";
			} else {
				$summary = "You have one unread email across $count accounts.\n";
				$long_summary = "You have one unread email across $count accounts. Here it is.\n";
			}
		} else {
			$count = count($accounts);
			if($count == 1) {
				$summary = "You have {$results['count']} unread {$accounts[0]->siriName} emails.\n";
				$long_summary = "You have {$results['count']} unread {$accounts[0]->siriName} emails. Here they are.\n";
			} else {
				$summary = "You have {$results['count']} unread emails across $count accounts.\n";
				$long_summary = "You have {$results['count']} unread emails across $count accounts. Here they are.\n";
			}
		}

		$email_objs = array();
		usort($all_emails, array('Email', 'sortByDateDescending'));
		foreach($all_emails as $email) {
			$long_summary .= $email->summary() . "\n";
			$email_objs[] = $email->object();
		}

		$results['summary'] = $summary;
		$results['long_summary'] = $long_summary;
		$results['emails'] = $email_objs;

		return $results;
	}

	function readMessage($account, $id)
	{
		$email = $account->inbox->getMail($id, false);
		$date_str = time2str(strtotime($email->date));
		$from = isset($email->fromName) ? $email->fromName : $email->fromAddress;
		$summary = "$date_str: an email from $from titled {$email->subject}, reads.\n";
		$plaintext = trim($email->textPlain);
		$summary .= (strlen($plaintext) == 0) ? 'There is no plain text verison of this email.' : $plaintext;

		$results = array();
		$results['summary'] = $summary;
		return $results;
	}

	function markEmailAsRead($account, $id)
	{
		$account->inbox->markMailAsRead($id);
		
		$results = array();
		$results['summary'] = 'Marked as read.';
		return $results;		
	}
	
	function markEmailAsUnread($account, $id)
	{
		$account->inbox->markMailAsUnread($id);
		
		$results = array();
		$results['summary'] = 'Marked as unnread.';
		return $results;		
	}
	
	function archiveEmail($account, $id)
	{
		$account->inbox->moveMail($id, $account->archiveFolder);

		$results = array();
		$results['summary'] = 'Message archived.';
		return $results;				
	}
	
	
	function deleteEmail($account, $id)
	{
		$account->inbox->moveMail($id, $account->trashFolder);
		
		$results = array();
		$results['summary'] = 'Message deleted.';
		return $results;			
	}

	function spamEmail($account, $id)
	{
		$account->inbox->moveMail($id, $account->spamFolder);
		
		$results = array();
		$results['summary'] = 'Message marked as spam.';
		return $results;			
	}

	// TODO: Need to copy the sent email to our Sent Mail folder.
	function replyToEmail($account, $id, $body)
	{
		$email = $account->inbox->getMail($id, false);
		$reply_to = $email->fromAddress;
		
		$mailer = new Swift_Mailer($account->SMTPTransport());
		
		$message = (new Swift_Message('Re: ' . $email->subject))
		  ->setFrom([$account->email => $account->fromName])
		  ->setTo([$reply_to])
		  ->setBody($body);
		
		$mailer->send($message);

		$results = array();
		$results['summary'] = 'Your reply was sent.';
		return $results;			
	}

    // Returns an English representation of a past date within the last month.
    // Graciously stolen from http://ejohn.org/files/pretty.js
    function time2str($ts)
    {
        if(!ctype_digit($ts))
            $ts = strtotime($ts);

        $diff = time() - $ts;
        if($diff == 0)
            return 'now';
        elseif($diff > 0)
        {
            $day_diff = floor($diff / 86400);
            if($day_diff == 0)
            {
                if($diff < 60) return 'just now';
                if($diff < 120) return '1 minute ago';
                if($diff < 3600) return floor($diff / 60) . ' minutes ago';
                if($diff < 7200) return '1 hour ago';
                if($diff < 86400) return floor($diff / 3600) . ' hours ago';
            }
            if($day_diff == 1) return 'Yesterday';
            if($day_diff < 7) return $day_diff . ' days ago';
            if($day_diff < 31) return ceil($day_diff / 7) . ' weeks ago';
            if($day_diff < 60) return 'last month';
            $ret = date('F Y', $ts);
            return ($ret == 'December 1969') ? '' : $ret;
        }
        else
        {
            $diff = abs($diff);
            $day_diff = floor($diff / 86400);
            if($day_diff == 0)
            {
                if($diff < 120) return 'in a minute';
                if($diff < 3600) return 'in ' . floor($diff / 60) . ' minutes';
                if($diff < 7200) return 'in an hour';
                if($diff < 86400) return 'in ' . floor($diff / 3600) . ' hours';
            }
            if($day_diff == 1) return 'Tomorrow';
            if($day_diff < 4) return date('l', $ts);
            if($day_diff < 7 + (7 - date('w'))) return 'next week';
            if(ceil($day_diff / 7) < 4) return 'in ' . ceil($day_diff / 7) . ' weeks';
            if(date('n', $ts) == date('n') + 1) return 'next month';
            $ret = date('F Y', $ts);
            return ($ret == 'December 1969') ? '' : $ret;
        }
    }
