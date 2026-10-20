<?php

declare(strict_types=1);

namespace EtoA\Message;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\MessageIgnore;
use EtoA\Entity\User;

class MessageIgnoreRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageIgnore::class);
    }

    public function isRecipientIgnoringSender(User $sender, User $recipient): bool
    {
        return $this->count(['owner'=>$recipient,'target'=>$sender]) > 0;
    }

}
