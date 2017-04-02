<?php

namespace EtoA\Tutorial;

use EtoA\Core\AbstractRepository;

class TutorialUserProgressRepository extends AbstractRepository
{
    public function hasFinishedTutorial($userId)
    {
        return $this->hasReadTutorial($userId, 2);
    }

    public function hasReadTutorial($userId, $tutorialId) {
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
}
