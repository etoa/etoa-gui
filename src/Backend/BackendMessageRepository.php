<?php

declare(strict_types=1);

namespace EtoA\Backend;

use EtoA\Core\AbstractRepository;

class BackendMessageRepository extends AbstractRepository
{
    public function addMessage(string $cmd, string $arg = ''): void
    {
        $this->getConnection()
            ->executeStatement(
                "INSERT IGNORE
                INTO backend_message_queue (cmd, arg)
                VALUES (?, ?);",
                [
                    $cmd, $arg,
                ]
            );
    }

    public function getMessageQueueSize(): int
    {
        return (int) $this->getConnection()
            ->executeQuery(
                "SELECT COUNT(id)
                FROM backend_message_queue;"
            )
            ->fetchOne();
    }
}
