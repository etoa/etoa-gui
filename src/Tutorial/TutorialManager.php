<?php

declare(strict_types=1);

namespace EtoA\Tutorial;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\TutorialText;

class TutorialManager extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TutorialText::class);
    }

    public function getTextById(int $id): ?TutorialText
    {
        $data = $this->createQueryBuilder('q')
            ->select(
                'text_id',
                'text_tutorial_id',
                'text_title',
                'text_content',
                'text_step'
            )
            ->where('text_id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();

        if ($data !== false) {
            $text = TutorialText::createFromArray($data);

            $prevStep = $this->createQueryBuilder('q')
                ->select('text_step')
                ->where('text_tutorial_id = :tutorialId')
                ->andWhere('text_step < :step')
                ->orderBy('text_step', 'DESC')
                ->setParameters([
                    'tutorialId' => $text->getTutorialId(),
                    'step' => $text->getStep(),
                ])
                ->getFirstResult();
            if ($prevStep !== false) {
                $text->prev = (int) $prevStep;
            }

            $nextStep = $this->createQueryBuilder('q')
                ->select('text_step')
                ->where('text_tutorial_id = :tutorialId')
                ->andWhere('text_step > :step')
                ->orderBy('text_step')
                ->setParameters([
                    'tutorialId' => $text->getTutorialId(),
                    'step' => $text->getStep(),
                ])
                ->getFirstResult();
            if ($nextStep !== false) {
                $text->next = (int) $nextStep;
            }

            return $text;
        }

        return null;
    }

    public function getText(int $tutorialId, int $step = 0): ?TutorialText
    {
        $id = $this->createQueryBuilder('q')
            ->select('text_id')
            ->where('text_tutorial_id = :tutorialId')
            ->andWhere('text_step <= :step')
            ->orderBy('text_step', 'DESC')
            ->setParameters([
                'tutorialId' => $tutorialId,
                'step' => $step,
            ])
            ->getFirstResult();

        if ($id !== false) {
            return $this->getTextById((int) $id);
        }

        return null;
    }
}
