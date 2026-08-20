<?php declare(strict_types=1);

namespace EtoA\Alliance\Board;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\AllianceBoardCategoryRank;

class AllianceBoardCategoryRankRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AllianceBoardCategoryRank::class);
    }

    /**
     * @return int[]
     */
    public function getCategoriesForRank(int $allianceId, int $rankId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('DISTINCT q.catId')
            ->innerJoin('App:AllianceRank', 'r', 'WITH', 'r.id = q.rankId')
            ->where('r.alliance = :allianceId')
            ->andWhere('r.id = :rankId')
            ->setParameters([
                'allianceId' => $allianceId,
                'rankId' => $rankId,
            ])
            ->getQuery()
            ->execute();

        return array_map(fn (array $row) => (int) $row['catId'], $data);
    }

    /**
     * @return int[]
     */
    public function getRanksForCategories(int $categoryId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('q.rankId')
            ->where('q.catId = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->getQuery()
            ->getArrayResult();

        return array_map(fn (array $row) => (int) $row['rankId'], $data);
    }

    /**
     * @return int[]
     */
    public function getRanksForBnd(int $bndId): array
    {
        return array_map('intval', $this->createQueryBuilder('q')
            ->select('q.rankId')
            ->where('q.bndId = :bndId')
            ->setParameter('bndId', $bndId)
            ->getQuery()
            ->getSingleColumnResult());
    }

    /**
     * @param int[] $rankIds
     */
    public function replaceRanks(int $categoryId, int $bndId, array $rankIds): void
    {
        if ($categoryId > 0) {
            $catRanks = $this->findBy(['catId'=>$categoryId]);
        } elseif ($bndId > 0) {
            $catRanks = $this->findBy(['bndId'=>$categoryId]);
        } else {
            throw new \InvalidArgumentException('Either category or bnd must be set');
        }

        foreach ($catRanks as $catRank) {
            $this->getEntityManager()->remove($catRank);
        }

        $count = count($rankIds);
        if ($count === 0) {
            return;
        }

        foreach ($rankIds as $rankId) {
            $catRank = new AllianceBoardCategoryRank();
            $catRank->setRankId($rankId);
            $catRank->setCatId($categoryId);
            $catRank->setBndId($bndId);

            $this->getEntityManager()->persist($catRank);
        }

        $this->save();
    }
}
