<?php

declare(strict_types=1);

namespace EtoA\Backend;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Alliance\Board\Category;
use EtoA\Core\AbstractRepository;

class BackendMessageRepository extends AbstractRepository
{
    //todo: create entity
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BackendMessageRepository::class);
    }

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
            ->fetchOne(
                "SELECT COUNT(id)
                FROM backend_message_queue;"
            );
    }
}
