<?php

declare(strict_types=1);

namespace EtoA\Support\Mail;

use EtoA\Core\Configuration\ConfigurationService;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\NullTransportFactory;
use Symfony\Component\Mailer\Transport\SendmailTransportFactory;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;
use Symfony\Component\Mailer\Transport\TransportFactoryInterface;

class MailServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[MailSenderService::class] = function (Container $pimple): MailSenderService {
            return new MailSenderService($pimple[ConfigurationService::class], $pimple[MailerInterface::class]);
        };

        $pimple[MailerInterface::class] = function (Container $pimple): MailerInterface {
            /** @var TransportFactoryInterface[] $transportFactory */
            $transportFactory = [
                new NullTransportFactory(),
                new SendmailTransportFactory(),
                new EsmtpTransportFactory(),
            ];

            $dsn = Dsn::fromString($_SERVER['MAILER_DSN']);
            foreach ($transportFactory as $factory) {
                if ($factory->supports($dsn)) {
                    return new Mailer($factory->create($dsn));
                }
            }

            throw new \RuntimeException('Mailer transport not supported');
        };
    }
}
