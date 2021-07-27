<?php

declare(strict_types=1);

namespace EtoA\Support\Mail;

use EtoA\Core\Configuration\ConfigurationService;
use Swift_Mailer;
use Swift_Message;
use Swift_SendmailTransport;
use Swift_SmtpTransport;
use Swift_Transport;

class MailSenderService
{
    private ConfigurationService $config;

    private Swift_Mailer $mailer;

    public function __construct(ConfigurationService $config)
    {
        $this->config = $config;
        $this->mailer = new Swift_Mailer($this->getMailTransport());
    }

    public function setMailer(Swift_Mailer $mailer): void
    {
        $this->mailer = $mailer;
    }

    /**
     * @param string|string[]|array<string,string> $recipients
     * @param null|string|string[]|array<string,string> $replyTo
     */
    public function send(string $subject, string $text, $recipients, $replyTo = null): int
    {
        $gameName = APP_NAME . ' ' . $this->config->get('roundname');
        $message = (new Swift_Message($gameName . ": " . $subject))
            ->setFrom([$this->config->get('mail_sender') => $gameName])
            ->setReplyTo($replyTo ?? [$this->config->get('mail_reply') => $gameName])
            ->setTo($recipients)
            ->setBody($text . $this->getSignature());

        return $this->mailer->send($message) ?? 0;
    }

    private function getMailTransport(): Swift_Transport
    {
        $host = $this->config->get('smtp_host');
        $port = $this->config->getInt('smtp_port');
        $username = $this->config->get('smtp_username');
        $password = $this->config->get('smtp_password');
        $security = $this->config->get('smtp_security');

        if (filled($host)) {
            return (new Swift_SmtpTransport($host, $port, filled($security) ? $security : null))
                ->setUsername(filled($username) ? $username : null)
                ->setPassword(filled($password) ? $password : null);
        }

        return new Swift_SendmailTransport();
    }

    private function getSignature(): string
    {
        return "

-----------------------------------------------------------------
Escape to Andromeda - Das Sci-Fi Browsergame - http://etoa.ch
Copyright (C) 2004 EtoA-Gaming, Schweiz
Kontakt: mail@etoa.ch
Forum: " . FORUM_URL;
    }
}
