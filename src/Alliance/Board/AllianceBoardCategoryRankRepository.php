<?php declare(strict_types=1);

namespace EtoA\Alliance\Board;

use EtoA\Core\AbstractRepository;

class AllianceBoardCategoryRankRepository extends AbstractRepository
{
    /**
     * @return int[]
     */
    public function getCategoriesForRank(int $allianceId, int $rankId): array
    {
        $data = $this->createQueryBuilder()
            ->select('DISTINCT c.cr_cat_id')
            ->from('alliance_ranks', 'r')
            ->innerJoin('r', 'allianceboard_catranks', 'c', 'r.rank_id = c.cr_rank_id')
            ->where('r.rank_alliance_id = :allianceId')
            ->andWhere('r.rank_id = :rankId')
            ->setParameters([
                'allianceId' => $allianceId,
                'rankId' => $rankId,
            ])
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => (int) $row['cr_cat_id'], $data);
    }

    /**
     * @return int[]
     */
    public function getRanksForCategories(int $categoryId): array
    {
        $data = $this->createQueryBuilder()
            ->select('cr_rank_id')
            ->from('allianceboard_catranks')
            ->where('cr_cat_id = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => (int) $row['cr_rank_id'], $data);
    }

    /**
     * @return int[]
     */
    public function getRanksForBnd(int $bndId): array
    {
        $data = $this->createQueryBuilder()
            ->select('cr_rank_id')
            ->from('allianceboard_catranks')
            ->where('cr_bnd_id = :bndId')
            ->setParameter('bndId', $bndId)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => (int) $row['cr_rank_id'], $data);
    }

    /**
     * @param int[] $rankIds
     */
    public function replaceRanks(int $categoryId, int $bndId, array $rankIds): void
    {
        $qb = $this->createQueryBuilder()
            ->delete('allianceboard_catranks');

        if ($categoryId > 0) {
            $qb
                ->where('cr_cat_id = :categoryId')
                ->setParameter('categoryId', $categoryId);
        } elseif ($bndId > 0) {
            $qb
                ->where('cr_bnd_id = :bndId')
                ->setParameter('bndId', $bndId);
        } else {
            throw new \InvalidArgumentException('Either category or bnd must be set');
        }

        $qb
            ->execute();

        $count = count($rankIds);
        if ($count === 0) {
            return;
        }

        $placeHolders = implode(',', array_fill(0, $count, '(?, ?, ?)'));
        $parameters = [];
        foreach ($rankIds as $rankId) {
            $parameters[] = $categoryId;
            $parameters[] = $bndId;
            $parameters[] = $rankId;
        }

        $this->getConnection()->executeQuery('INSERT INTO allianceboard_catranks (cr_cat_id, cr_bnd_id, cr_rank_id) VALUES ' . $placeHolders, $parameters);
    }
}
