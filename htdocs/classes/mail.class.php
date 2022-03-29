<?php
class Mail
{
	/** @var string */
	private $body;
	/** @var string */
	private $headers;
    /** @var Swift_Transport_EsmtpTransport */
    private $transport;

	public function __construct($subject,$text,$useTemplate=1)
	{
        if (file_exists(__DIR__ . '/../config/mail.json')) {
            $config = json_decode(file_get_contents(__DIR__ . '/../config/mail.json'), true);
            $this->transport = (new Swift_SmtpTransport($config['host'], $config['port'], $config['encryption']))
                ->setUsername($config['username'])
                ->setPassword($config['password']);
        } else {
            $this->transport = new Swift_SendmailTransport();
        }

		$this->subject = APP_NAME.' '.Config::getInstance()->roundname->v.": ".$subject;
		if ($useTemplate)
		{
			$this->body.= $text."

-----------------------------------------------------------------
Escape to Andromeda - Das Sci-Fi Browsergame - http://etoa.ch
Copyright (C) 2004 EtoA-Gaming, Schweiz
Kontakt: mail@etoa.ch
Forum: ".FORUM_URL;
		}
		else
		{
			$this->body = $text;
		}
	}

	function send($rcpt,$replyTo="")
	{
		$replyTo = trim($replyTo);
		if ($replyTo!="") {
            $replyTo = [$replyTo];
		}
		else
		{
            $replyTo = [Config::getInstance()->mail_reply->v => APP_NAME.' '.Config::getInstance()->roundname->v];
		}

        $message = (new Swift_Message($this->subject))
            ->setFrom([Config::getInstance()->mail_sender->v => APP_NAME.' '.Config::getInstance()->roundname->v])
            ->setReplyTo($replyTo)
            ->setTo($rcpt)
            ->setBody($this->body);

        try {
            (new Swift_Mailer($this->transport))->send($message);

            return true;
        } catch (\Throwable $e) {
            error_msg("Mail wurde nicht gesendet!\n\n".$message->toString() . $e->getMessage());
        }


		return false;
	}
}
