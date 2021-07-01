<?php

declare(strict_types=1);

namespace EtoA\Help\TicketSystem;

use EtoA\Admin\AdminUserRepository;
use EtoA\Message\MessageRepository;
use EtoA\User\UserRepository;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class TicketSystemServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['etoa.help.ticket.repository'] = function (Container $pimple): TicketRepository {
            return new TicketRepository(
                $pimple['db']
            );
        };
        $pimple['etoa.help.ticket.message.repository'] = function (Container $pimple): TicketMessageRepository {
            return new TicketMessageRepository(
                $pimple['db']
            );
        };
        $pimple['etoa.help.ticket.service'] = function (Container $pimple): TicketService {
            return new TicketService(
                $pimple['etoa.help.ticket.repository'],
                $pimple['etoa.help.ticket.message.repository'],
                $pimple[AdminUserRepository::class],
                $pimple[UserRepository::class],
                $pimple[MessageRepository::class]
            );
        };
    }
}
