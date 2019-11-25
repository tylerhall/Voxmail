# Voxmail - A Siri Shortcut for triaging your email inbox

Please see the [accompanying blog post](https://tyler.io/triage-your-email-in-the-car-with-siri/) for the dumb reasons behind this project being a thing. But, in a nutshell...

Siri’s support for handling email - particularly when using CarPlay - is sub par. I thought about writing a native app to handle this but didn’t want to go through the trouble. I just wanted to make something quickly that works.

So, it’s a ridiculous PHP script that relies on the [PHP IMAP](https://github.com/barbushin/php-imap) and [SwiftMailer](https://swiftmailer.symfony.com) open source projects on the backend to handle the IMAP and SMTP connections. And [a rather large Siri Shortcut](https://cdn.tyler.io/wp-content/uploads/2019/11/voxmail-shortcut.jpg) on your iPhone that you can invoke and command with your voice.

You can listen to a summary of your unread inbox messages and take action on them. Currently supported commands are:

* `Read`: Listen to the full plaintext body. (Anyone have suggestions on how to handle HTML emails? Maybe send it through [Mercury Parser](https://github.com/postlight/mercury-parser) or something?)
* `Archive`: Moves the email to a user-defined archive folder.
* `Delete`: Deletes the email.
* `Spam`: Moves the email to a user-defined junk mail folder.
* `Mark as read`
* `Mark as unread`
* `Reply`: Prompts for a response via Siri and then replies with that message to the original sender.

Note: Because of the way Siri and this script are implemented, you need to speak those commands when promoted exactly as shown above. For example: if you say "Read it" instead of "Read", the Shortcut won't know what to do. I'm open to suggestions on how to improve this.

## Limitations

While the script _does_ support multiple email accounts, my motivation for this project was specifically to triage the unread emails in my inbox. 

Thus, it ignores read messages. Also, you can’t check email in other (not your inbox) folders. You could certainly hack this by adding a new account with the same credentials that points your inbox folder to a _different_ folder. There are currently hooks in place to possibly support navigating different folders in the future. But that’s not fully thought out yet.

Only IMAP and SMTP are supported (SSL connections _do_ work). Sorry, Exchange users. Pull requests are welcome!

When you send a reply, the sent email is _not_ copied into your sent folder. I plan on fixing this eventually, but, again, I’d be happy to accept a fix from anyone who wants to contribute one.

## Installation

I’ve only tested this on a plain-ole Ubuntu 18.04 box. So, translate the following commands to your particular system:

**On your web server:**

1. Clone the repo to somewhere web accessible.
2. You’ll need the PHP IMAP extension. For me, that was as simple as `sudo apt install php7.2-imap`
3. Change into the project directory and install the two [Composer](https://getcomposer.org) dependencies.

	composer require php-imap/php-imap
	composer require "swiftmailer/swiftmailer:^6.0"

4. Edit `config.sample.php` with the appropriate settings and rename it to `config.php` . This includes adding the credentials for your email accounts and (very important!) setting [a long, random string](https://www.random.org/strings/?num=5&len=20&digits=on&upperalpha=on&loweralpha=on&unique=on&format=html&rnd=new) for the `SECRET` constant to prevent unauthorized access. Also, set `BASE_URL` to the web-accessible URL of the `index.php` script.

**On your iPhone:**

1. [Download the Siri Shortcut](https://www.icloud.com/shortcuts/01310f0ad4854b95935fa9d106ffc9b8) and answer the two questions. You'll need to provide the `BASE_URL` and `SECRET` you defined in `config.php`.
2. By default, you can invoke the Shortcut by telling Siri "Check my email". Remember: because this phrase is extremely similar to how you might otherwise ask Siri to check the system Mail.app, if you don't speak the phrase exactly as written in the title of the Shortcut, Siri may try and use Mail.app instead - which mostly doesn't cooperate with CarPlay. So, if you say "Check my emails" (plural), it will fail.

## Feedback

I’m very much open to suggestions for improving the script. Feel free to open a pull request, file an issue, or [get in touch directly](https://tyler.io/about/).
