<?php declare(strict_types=1);

namespace EtoA\Tutorial;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\TutorialUserProgress;

class TutorialUserProgressRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TutorialUserProgress::class);
    }

    public function hasFinishedTutorial(int $userId): bool
    {
        return $this->hasReadTutorial($userId, 2);
    }

    public function hasReadTutorial(int $userId, int $tutorialId): bool
    {
        return (bool) $this->createQueryBuilder('q')
            ->select('tup_closed')
            ->where('tup_user_id = :userId')
            ->andWhere('tup_tutorial_id = :tutorialId')
            ->setParameters([
                'userId' => $userId,
                'tutorialId' => $tutorialId,
            ])
            ->getFirstResult();
    }

    public function closeTutorial(int $userId, int $tutorial): void
    {
        $this->createQueryBuilder('q')
            ->update('tutorial_user_progress')
            ->set('tup_closed', ':closed')
            ->where('tup_user_id = :userId')
            ->andWhere('tup_tutorial_id = :tutorialId')
            ->setParameters([
                'closed' => 1,
                'userId' => $userId,
                'tutorialId' => $tutorial,
            ])
            ->executeQuery();
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
        $data = $this->createQueryBuilder('q')
            ->select('tup_text_step')
            ->from('tutorial_user_progress')
            ->where('tup_user_id = :userId')
            ->andWhere('tup_tutorial_id = :tutorialId')
            ->setParameters([
                'userId' => $userId,
                'tutorialId' => $tutorialId,
            ])
            ->fetchOne();

        if ($data !== null) {
            return (int) $data;
        }

        return 0;
    }

    public function reopenTutorial(int $userId, int $tutorialId): void
    {
        $this->createQueryBuilder('q')
            ->update('tutorial_user_progress')
            ->set('tup_closed', (string) 0)
            ->where('tup_user_id = :userId')
            ->andWhere('tup_tutorial_id = :tutorialId')
            ->setParameters([
                'userId' => $userId,
                'tutorialId' => $tutorialId,
            ])
            ->executeQuery();
    }

    public function reopenAllTutorials(int $userId): void
    {
        $this->createQueryBuilder('q')
            ->update('tutorial_user_progress')
            ->set('tup_closed', (string) 0)
            ->where('tup_user_id = :userId')
            ->setParameters([
                'userId' => $userId,
            ])
            ->executeQuery();
    }
}
