<?php declare(strict_types=1);

namespace EtoA\Tip;

use EtoA\Core\AbstractRepository;

class TipRepository extends AbstractRepository
{
    public function getRandomTipText(): ?string
    {
        $text = $this->getConnection()->fetchOne("SELECT tip_text FROM tips WHERE tip_active = 1 ORDER BY RAND() LIMIT 1");

        return $text !== false ? $text : null;
    }
}
