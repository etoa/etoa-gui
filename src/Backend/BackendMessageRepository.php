<?php

declare(strict_types=1);

namespace EtoA\Backend;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\BackendMessage;

class BackendMessageRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BackendMessage::class);
    }

    public function addMessage(string $cmd, string $arg = ''): void
    {
        if(!$this->findOneBy(['cmd'=>$cmd,'arg'=>$arg])) {
            $message = new BackendMessage();
            $message->setCmd($cmd);
            $message->setArg($arg);

            $this->persist($message);
            $this->save();
        }
    }

    public function getMessageQueueSize(): int
    {
        return $this->count([]);
    }
}
