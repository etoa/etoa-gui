<?php

declare(strict_types=1);

namespace EtoA\Support\Mail;

use EtoA\Core\Configuration\ConfigurationService;
use Exception;

class MailSenderService
{
    private ConfigurationService $config;

    public function __construct(ConfigurationService $config)
    {
        $this->config = $config;
    }

    /**
     * @param string|string[] $recipients
     */
    public function send(string $subject, string $text, $recipients, ?string $replyTo = null): void
    {
        $subject = $this->getSubjectPrefix() . $subject;
        $body = $text . $this->getSignature();
        $headers = [
            "From" => $this->getForm(),
            "Content-Type" => "text/plain; charset=UTF-8",
            "MIME-Version" => "1.0",
            "Content-Transfer-Encoding" => "8bit",
            "X-Mailer" => "PHP",
            "Reply-to" => $replyTo ?? $this->getReplyTo(),
        ];
        foreach ((is_array($recipients) ? $recipients : [$recipients]) as $to) {
            if (!mail($to, $subject, $body, $headers)) {
                throw new Exception("Mail wurde nicht gesendet!\n\nTo:" . $to . "\nSubject:" . $subject . "\n\nBody:\n\n" . $body);
            }
        }
    }

    private function getForm(): string
    {
        return APP_NAME . ' ' . $this->config->get('roundname') . "<" . $this->config->get('mail_sender') . ">";
    }

    private function getReplyTo(): string
    {
        return APP_NAME . ' ' . $this->config->get('roundname') . "<" . $this->config->get('mail_reply') . ">";
    }

    private function getSubjectPrefix(): string
    {
        return APP_NAME . ' ' . $this->config->get('roundname') . ": ";
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
