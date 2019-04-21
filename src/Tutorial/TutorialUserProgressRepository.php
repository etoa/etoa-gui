<?php declare(strict_types=1);

namespace EtoA\Tutorial;

use EtoA\Core\AbstractRepository;

class TutorialUserProgressRepository extends AbstractRepository
{
    public function hasFinishedTutorial(int $userId): bool
    {
        return $this->hasReadTutorial($userId, 2);
    }

    public function hasReadTutorial(int $userId, int $tutorialId): bool
    {
        return (bool)$this->createQueryBuilder()
            ->select('tup_closed')
            ->from('tutorial_user_progress')
            ->where('tup_user_id = :userId')
            ->andWhere('tup_tutorial_id = :tutorialId')
            ->setParameters([
                'userId' => $userId,
                'tutorialId' => $tutorialId,
            ])
            ->execute()
            ->fetchColumn();
    }

    public function closeTutorial(int $userId, int $tutorial): void
    {
        $this->createQueryBuilder()
            ->update('tutorial_user_progress')
            ->set('tup_closed', ':closed')
            ->where('tup_user_id = :userId')
            ->andWhere('tup_tutorial_id = :tutorialId')
            ->setParameters([
                'closed' => 1,
                'userId' => $userId,
                'tutorialId' => $tutorial,
            ])
            ->execute();
    }
}
