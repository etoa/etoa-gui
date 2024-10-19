<?php declare(strict_types=1);

namespace EtoA\Alliance;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;

class AlliancePollRepository extends AbstractRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AlliancePoll::class);
    }

    public function add(int $allianceId, string $title, string $question, string $answer1, string $answer2, string $answer3, string $answer4, string $answer5, string $answer6, string $answer7, string $answer8): int
    {
        $this->createQueryBuilder('q')
            ->insert('alliance_polls')
            ->values([
                'poll_alliance_id' => ':allianceId',
                'poll_title' => ':title',
                'poll_question' => ':question',
                'poll_timestamp' => ':timestamp',
                'poll_a1_text' => ':answer1',
                'poll_a2_text' => ':answer2',
                'poll_a3_text' => ':answer3',
                'poll_a4_text' => ':answer4',
                'poll_a5_text' => ':answer5',
                'poll_a6_text' => ':answer6',
                'poll_a7_text' => ':answer7',
                'poll_a8_text' => ':answer8',
            ])
            ->setParameters([
                'allianceId' => $allianceId,
                'title' => $title,
                'question' => $question,
                'timestamp' => time(),
                'answer1' => $answer1,
                'answer2' => $answer2,
                'answer3' => $answer3,
                'answer4' => $answer4,
                'answer5' => $answer5,
                'answer6' => $answer6,
                'answer7' => $answer7,
                'answer8' => $answer8,
            ])
            ->executeQuery();

        return (int) $this->getConnection()->lastInsertId();
    }

    /**
     * @return AlliancePoll[]
     */
    public function getPolls(int $allianceId, int $limit = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->select('*')
            ->from('alliance_polls')
            ->where('poll_alliance_id = :allianceId')
            ->setParameters([
                'allianceId' => $allianceId,
            ])
            ->orderBy('poll_timestamp', 'DESC');

        if ($limit > 0) {
            $qb->setMaxResults($limit);
        }

        $data = $qb
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new AlliancePoll($row), $data);
    }

    public function getPoll(int $pollId, int $allianceId): ?AlliancePoll
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('alliance_polls')
            ->where('poll_id = :id')
            ->andWhere('poll_alliance_id = :allianceId')
            ->setParameters([
                'id' => $pollId,
                'allianceId' => $allianceId,
            ])
            ->fetchAssociative();

        return $data !== false ? new AlliancePoll($data) : null;
    }

    /**
     * @return array<int, int>
     */
    public function getUserVotes(int $userId, int $allianceId): array
    {
        $rows = $this->createQueryBuilder('q')
            ->select('vote_poll_id, vote_id')
            ->from('alliance_poll_votes')
            ->where('vote_user_id = :userId')
            ->andWhere('vote_alliance_id = :allianceId')
            ->setParameters([
                'userId' => $userId,
                'allianceId' => $allianceId,
            ])
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $rows);
    }

    public function updateActive(int $pollId, int $allianceId, bool $active): int
    {
        return $this->createQueryBuilder('q')
            ->update('alliance_polls')
            ->set('poll_active', ':active')
            ->where('poll_id = :id')
            ->andWhere('poll_alliance_id = :allianceId')
            ->setParameters([
                'id' => $pollId,
                'allianceId' => $allianceId,
                'active' => (int) $active,
            ])
            ->executeQuery()
            ->rowCount();
    }

    public function updatePoll(int $pollId, int $allianceId, string $title, string $question, string $answer1, string $answer2, string $answer3, string $answer4, string $answer5, string $answer6, string $answer7, string $answer8): int
    {
        return $this->createQueryBuilder('q')
            ->update('alliance_polls')
            ->set('poll_title', ':title')
            ->set('poll_question', ':question')
            ->set('poll_a1_text', ':answer1')
            ->set('poll_a2_text', ':answer2')
            ->set('poll_a3_text', ':answer3')
            ->set('poll_a4_text', ':answer4')
            ->set('poll_a5_text', ':answer5')
            ->set('poll_a6_text', ':answer6')
            ->set('poll_a7_text', ':answer7')
            ->set('poll_a8_text', ':answer8')
            ->where('poll_id = :id')
            ->andWhere('poll_alliance_id = :allianceId')
            ->setParameters([
                'id' => $pollId,
                'allianceId' => $allianceId,
                'title' => $title,
                'question' => $question,
                'answer1' => $answer1,
                'answer2' => $answer2,
                'answer3' => $answer3,
                'answer4' => $answer4,
                'answer5' => $answer5,
                'answer6' => $answer6,
                'answer7' => $answer7,
                'answer8' => $answer8,
            ])
            ->executeQuery()
            ->rowCount();
    }

    public function addVote(int $pollId, int $allianceId, int $userId, int $answerId): bool
    {
        if ($answerId < 1 || $answerId > 8) {
            throw new \InvalidArgumentException('Invalid answer id');
        }

        $field = sprintf('poll_a%s_count', $answerId);

        $voted = (bool) $this->createQueryBuilder('q')
            ->update('alliance_polls')
            ->set($field, $field . ' + 1')
            ->where('poll_alliance_id = :allianceId')
            ->andWhere('poll_id = :id')
            ->setParameters([
                'id' => $pollId,
                'allianceId' => $allianceId,
            ])
            ->executeQuery()
            ->rowCount();

        if ($voted) {
            $this->createQueryBuilder('q')
                ->insert('alliance_poll_votes')
                ->values([
                    'vote_poll_id' => ':pollId',
                    'vote_user_id' => ':userId',
                    'vote_alliance_id' => ':allianceId',
                    'vote_number' => ':vote',
                ])
                ->setParameters([
                    'pollId' => $pollId,
                    'allianceId' => $allianceId,
                    'userId' => $userId,
                    'vote' => $answerId,
                ])
                ->executeQuery();
        }

        return $voted;
    }

    public function deletePoll(int $pollId, int $allianceId): bool
    {
        $deleted = (bool) $this->createQueryBuilder('q')
            ->delete('alliance_polls')
            ->where('poll_id = :id')
            ->andWhere('poll_alliance_id = :allianceId')
            ->setParameters([
                'id' => $pollId,
                'allianceId' => $allianceId,
            ])
            ->executeQuery()
            ->rowCount();

        if ($deleted) {
            $this->createQueryBuilder('q')
                ->delete('alliance_poll_votes')
                ->where('vote_poll_id = :id')
                ->andWhere('vote_alliance_id = :allianceId')
                ->setParameters([
                    'id' => $pollId,
                    'allianceId' => $allianceId,
                ])
                ->executeQuery();
        }

        return $deleted;
    }

    public function deleteAllianceEntries(int $allianceId): void
    {
        $this->createQueryBuilder('q')
            ->delete('alliance_polls')
            ->where('poll_alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->executeQuery();

        $this->createQueryBuilder('q')
            ->delete('alliance_poll_votes')
            ->where('vote_alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->executeQuery();
    }
}
