<?php

declare(strict_types=1);

namespace EtoA\Support\Mail;

use EtoA\Core\AppName;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\ExternalUrl;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class MailSenderService
{
    public function __construct(
        private ConfigurationService $config,
        private MailerInterface $mailer,
    ) {
    }

    /**
     * @param string|string[]|array<string,string> $recipients
     * @param null|string|string[]|array<string,string> $replyTo
     */
    public function send(string $subject, string $text, $recipients, $replyTo = null): void
    {
        $gameName = AppName::NAME . ' ' . $this->config->get('roundname');

        $message = (new Email())
            ->from(new Address((string) $this->config->get('mail_sender'), $gameName))
            ->replyTo($this->convertAddressArrays($replyTo, new Address((string) $this->config->get('mail_reply'), $gameName))[0])
            ->subject($gameName . ": " . $subject)
            ->text($text . $this->getSignature());

        if (is_array($recipients) && count($recipients) > 1) {
            $message = $message->bcc(...$this->convertAddressArrays($recipients));
        } else {
            $message = $message->to(...$this->convertAddressArrays($recipients));
        }

        $this->mailer->send($message);
    }

    /**
     * @param null|string|string[]|array<string,string> $emails
     * @return Address[]
     */
    private function convertAddressArrays($emails, Address $default = null): array
    {
        if ($emails === null && $default !== null) {
            return [$default];
        }

        if (is_array($emails)) {
            $addresses = [];
            foreach ($emails as $email => $name) {
                $addresses[] = new Address($email, $name);
            }

            return $addresses;
        }

        if ($emails !== null) {
            return [new Address($emails)];
        }

        throw new \InvalidArgumentException('Unknown email recipient');
    }

    private function getSignature(): string
    {
        return "

-----------------------------------------------------------------
Escape to Andromeda - Das Sci-Fi Browsergame - http://etoa.ch
Copyright (C) 2004 EtoA-Gaming, Schweiz
Kontakt: mail@etoa.ch
Forum: " . ExternalUrl::FORUM;
    }
}
