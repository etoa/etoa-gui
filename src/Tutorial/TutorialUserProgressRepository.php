<?php declare(strict_types=1);

namespace EtoA\Tutorial;

use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Tutorial;
use EtoA\Entity\TutorialUserProgress;
use EtoA\Entity\User;

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
            ->select('q.closed')
            ->where('q.userId = :userId')
            ->andWhere('q.tutorialId = :tutorialId')
            ->setParameters([
                'userId' => $userId,
                'tutorialId' => $tutorialId,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);
    }

    public function closeTutorial(User $user, Tutorial $tutorial): void
    {
        $this->getOrCreateProgress($user, $tutorial)->setClosed(true);

        $this->save();
    }

    public function setUserProgress(User $user, Tutorial $tutorial, int $textStep): void
    {
        $this->getOrCreateProgress($user, $tutorial)->setTextStep($textStep);

        $this->save();
    }

    /**
     * UserTwigSubscriber shows a tutorial exactly while the user has no progress row, so
     * every write has to be able to create one - otherwise nothing is persisted at all.
     */
    private function getOrCreateProgress(User $user, Tutorial $tutorial): TutorialUserProgress
    {
        $progress = $this->findOneBy(['user' => $user, 'tutorial' => $tutorial]);
        if ($progress !== null) {
            return $progress;
        }

        $progress = new TutorialUserProgress();
        // tup_user_id and tup_tutorial_id are each mapped twice, as an id field and as the
        // join column of an association. Both sides must be set, otherwise one overwrites
        // the other with null while building the insert.
        $progress->setUser($user);
        $progress->setUserId((int) $user->getId());
        $progress->setTutorial($tutorial);
        $progress->setTutorialId((int) $tutorial->getId());
        $progress->setTextStep(0);

        $this->persist($progress);

        return $progress;
    }

    public function getUserProgress(User $user, Tutorial $tutorial): int
    {
        $progress = $this->findOneBy(['user'=>$user,'tutorial'=>$tutorial]);

        return $progress?$progress->getTextStep():0;
    }

    public function reopenTutorial(int $userId, int $tutorialId): void
    {
        $this->createQueryBuilder('q')
            ->update()
            ->set('q.closed', ':closed')
            ->where('q.userId = :userId')
            ->andWhere('q.tutorialId = :tutorialId')
            ->setParameters([
                'closed' => false,
                'userId' => $userId,
                'tutorialId' => $tutorialId,
            ])
            ->getQuery()
            ->execute();
    }

    public function reopenAllTutorials(int $userId): void
    {
        $this->createQueryBuilder('q')
            ->update()
            ->set('q.closed', ':closed')
            ->where('q.userId = :userId')
            ->setParameters([
                'closed' => false,
                'userId' => $userId,
            ])
            ->getQuery()
            ->execute();
    }
}
