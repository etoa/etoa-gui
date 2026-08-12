<?php

declare(strict_types=1);

namespace EtoA\Tutorial;

use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Tutorial;
use EtoA\Entity\TutorialText;

class TutorialManager extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TutorialText::class);
    }

    public function getTextById(int $id): ?TutorialText
    {
        $text = $this->find($id);
        $tutorial = $text?->getTutorial();
        if ($text === null || $tutorial === null) {
            return null;
        }

        return $this->withNeighbours($text, $tutorial);
    }

    /**
     * The text at the given step, or the closest earlier one.
     */
    public function getText(Tutorial $tutorial, int $step = 0): ?TutorialText
    {
        $text = $this->createQueryBuilder('q')
            ->where('q.tutorial = :tutorial')
            ->andWhere('q.step <= :step')
            ->orderBy('q.step', 'DESC')
            ->setParameters([
                'tutorial' => $tutorial,
                'step' => $step,
            ])
            // without this every step but the lowest matches several rows and
            // getOneOrNullResult() throws NonUniqueResultException
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($text === null) {
            return null;
        }

        return $this->withNeighbours($text, $tutorial);
    }

    /**
     * The client shows its "Zurück"/"Weiter" buttons based on these.
     */
    private function withNeighbours(TutorialText $text, Tutorial $tutorial): TutorialText
    {
        $text->prev = $this->findNeighbourStep($tutorial, (int) $text->getStep(), false);
        $text->next = $this->findNeighbourStep($tutorial, (int) $text->getStep(), true);

        return $text;
    }

    /**
     * Step of the text right before or right after the given one, null if there is none.
     */
    private function findNeighbourStep(Tutorial $tutorial, int $step, bool $forward): ?int
    {
        $neighbour = $this->createQueryBuilder('q')
            ->select('q.step')
            ->where('q.tutorial = :tutorial')
            ->andWhere($forward ? 'q.step > :step' : 'q.step < :step')
            ->orderBy('q.step', $forward ? 'ASC' : 'DESC')
            ->setParameters([
                'tutorial' => $tutorial,
                'step' => $step,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);

        return $neighbour === null ? null : (int) $neighbour;
    }
}
