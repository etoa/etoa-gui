<?php declare(strict_types=1);

namespace EtoA\Tip;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Tip;

class TipRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tip::class);
    }

    public function getRandomTipText(): ?string
    {
        $text = $this->getConnection()->fetchOne("SELECT tip_text FROM tips WHERE tip_active = 1 ORDER BY RAND() LIMIT 1");

        return $text !== false ? $text : null;
    }
}
