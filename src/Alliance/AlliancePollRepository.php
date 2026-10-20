<?php declare(strict_types=1);

namespace EtoA\Alliance;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Alliance;
use EtoA\Entity\AlliancePoll;

class AlliancePollRepository extends AbstractRepository
{

    public function __construct(
        ManagerRegistry $registry,
        private readonly AlliancePollVotesRepository $alliancePollVotesRepository
    )
    {
        parent::__construct($registry, AlliancePoll::class);
    }

    /**
     * @return AlliancePoll[]
     */
    public function getPolls(Alliance $alliance, int $limit = null): array
    {
        return $this->findBy(['alliance'=>$alliance],['timestamp'=>'DESC'],$limit);
    }

    public function updateActive(AlliancePoll $poll, bool $active): void
    {
        $poll->setActive($active);
        $this->save();
    }

    public function deletePoll(AlliancePoll $poll): void
    {
        $pollVotes = $this->alliancePollVotesRepository->findBy(['poll'=>$poll]);

        if($pollVotes) {
            foreach ($pollVotes as $pollVote)
                $this->alliancePollVotesRepository->remove($pollVote);
            $this->alliancePollVotesRepository->save();
        }

        $this->remove($poll);
        $this->save();
    }

    public function deleteAllianceEntries(Alliance $alliance): void
    {
        $polls = $this->findBy(['alliance'=>$alliance]);

        foreach ($polls as $poll) {
            $this->remove($poll);
        }

        $this->save();

        $votes = $this->alliancePollVotesRepository->findBy(['alliance'=>$alliance]);

        foreach ($votes as $vote) {
            $this->remove($vote);
        }

        $this->alliancePollVotesRepository->save();
    }
}
