<?php

declare(strict_types=1);

namespace EtoA\Support\Mail;

use EtoA\Core\Configuration\ConfigurationService;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class MailServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[MailSenderService::class] = function (Container $pimple): MailSenderService {
            return new MailSenderService($pimple[ConfigurationService::class]);
        };
    }
}
