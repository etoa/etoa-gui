<?php
class Mail
{
	function __construct($subject,$text,$useTemplate=1)
	{
		$this->subject = Constants::getInstance()->appName.' '.Config::getInstance()->roundname->v.": ".$subject;
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
		$this->headers = "From: ".Constants::getInstance()->appName.' '.Config::getInstance()->roundname->v."<".Config::getInstance()->mail_sender->v.">\n";
		$this->headers.= "Content-Type: text/plain; charset=UTF-8\n";
		$this->headers.= "MIME-Version: 1.0\n";
		$this->headers.= "Content-Transfer-Encoding: 8bit\n";
		$this->headers.= "X-Mailer: PHP\n";
	}

	function send($rcpt,$replyTo="")
	{
		if ($replyTo!="")
		{
			$headers = $this->headers."Reply-to: ".$replyTo."\n";
		}
		else
		{
			$headers = $this->headers."Reply-to: ".Constants::getInstance()->appName.' '.Config::getInstance()->roundname->v."<".Config::getInstance()->mail_reply->v.">\n";
		}
		if (mail($rcpt,$this->subject,$this->body,$headers))
			return true;

		err_msg("Mail wurde nicht gesendet!\n\nTo: $rcpt\nSubject:".$this->subject."\n\nHeader:\n\n$headers\n\nBody:\n\n".$this->body);
		return false;



	}


}
?>
