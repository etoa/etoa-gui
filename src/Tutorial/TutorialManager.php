<?php

declare(strict_types=1);

namespace EtoA\Tutorial;

use EtoA\Core\AbstractRepository;

class TutorialManager extends AbstractRepository
{
    public function getTextById(int $id): ?TutorialText
    {
        $data = $this->createQueryBuilder()
            ->select(
                'text_id',
                'text_tutorial_id',
                'text_title',
                'text_content',
                'text_step'
            )
            ->from('tutorial_texts')
            ->where('text_id = :id')
            ->setParameter('id', $id)
            ->execute()
            ->fetchAssociative();

        if ($data !== false) {
            $text = TutorialText::createFromArray($data);

            $prevStep = $this->createQueryBuilder()
                ->select('text_step')
                ->from('tutorial_texts')
                ->where('text_tutorial_id = :tutorialId')
                ->andWhere('text_step < :step')
                ->orderBy('text_step', 'DESC')
                ->setParameters([
                    'tutorialId' => $text->tutorialId,
                    'step' => $text->step,
                ])
                ->execute()
                ->fetchOne();
            if ($prevStep !== false) {
                $text->prev = (int) $prevStep;
            }

            $nextStep = $this->createQueryBuilder()
                ->select('text_step')
                ->from('tutorial_texts')
                ->where('text_tutorial_id = :tutorialId')
                ->andWhere('text_step > :step')
                ->orderBy('text_step')
                ->setParameters([
                    'tutorialId' => $text->tutorialId,
                    'step' => $text->step,
                ])
                ->execute()
                ->fetchOne();
            if ($nextStep !== false) {
                $text->next = (int) $nextStep;
            }

            return $text;
        }

        return null;
    }

    public function getText(int $tutorialId, int $step = 0): ?TutorialText
    {
        $id = $this->createQueryBuilder()
            ->select('text_id')
            ->from('tutorial_texts')
            ->where('text_tutorial_id = :tutorialId')
            ->andWhere('text_step <= :step')
            ->orderBy('text_step', 'DESC')
            ->setParameters([
                'tutorialId' => $tutorialId,
                'step' => $step,
            ])
            ->execute()
            ->fetchOne();

        if ($id !== false) {
            return $this->getTextById((int) $id);
        }

        return null;
    }

    public function setUserProgress(int $userId, int $tutorialId, int $textStep): void
    {
        $this->getConnection()->executeStatement(
            'REPLACE INTO
                tutorial_user_progress
            (
                tup_user_id,
                tup_tutorial_id,
                tup_text_step)
            VALUES (
                :userId,
                :tutorialId,
                :textStep
            );',
            [
                'userId' => $userId,
                'tutorialId' => $tutorialId,
                'textStep' => $textStep,
            ]
        );
    }

    public function getUserProgress(int $userId, int $tutorialId): int
    {
        $data = $this->createQueryBuilder()
            ->select('tup_text_step')
            ->from('tutorial_user_progress')
            ->where('tup_user_id = :userId')
            ->andWhere('tup_tutorial_id = :tutorialId')
            ->setParameters([
                'userId' => $userId,
                'tutorialId' => $tutorialId,
            ])
            ->execute()
            ->fetchOne();

        if ($data !== null) {
            return (int) $data;
        }

        return 0;
    }

    public function hasReadTutorial(int $userId, int $tutorialId): bool
    {
        $data = $this->createQueryBuilder()
            ->select('tup_closed')
            ->from('tutorial_user_progress')
            ->where('tup_user_id = :userId')
            ->andWhere('tup_tutorial_id = :tutorialId')
            ->setParameters([
                'userId' => $userId,
                'tutorialId' => $tutorialId,
            ])
            ->execute()
            ->fetchOne();

        if ($data !== false) {
            return $data == 1;
        }

        return false;
    }

    public function reopenTutorial(int $userId, int $tutorialId): void
    {
        $this->createQueryBuilder()
            ->update('tutorial_user_progress')
            ->set('tup_closed', (string) 0)
            ->where('tup_user_id = :userId')
            ->andWhere('tup_tutorial_id = :tutorialId')
            ->setParameters([
                'userId' => $userId,
                'tutorialId' => $tutorialId,
            ])
            ->execute();
    }

    public function reopenAllTutorials(int $userId): void
    {
        $this->createQueryBuilder()
            ->update('tutorial_user_progress')
            ->set('tup_closed', (string) 0)
            ->where('tup_user_id = :userId')
            ->setParameters([
                'userId' => $userId,
            ])
            ->execute();
    }
}
