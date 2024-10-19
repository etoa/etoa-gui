<?php declare(strict_types=1);

namespace EtoA\Alliance\Board;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;

class AllianceBoardCategoryRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @param int[] $categoryIds
     * @return array<int, int>
     */
    public function getCategoryPostCounts(array $categoryIds): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('t.topic_cat_id, COUNT(p.post_id)')
            ->from('allianceboard_topics', 't')
            ->innerJoin('t', 'allianceboard_posts', 'p', 'p.post_topic_id = t.topic_id')
            ->where('t.topic_cat_id IN (:categoryIds)')
            ->groupBy('t.topic_cat_id')
            ->setParameter('categoryIds', $categoryIds, ArrayParameterType::INTEGER)
            ->fetchAllKeyValue();

        $counts = [];
        foreach ($categoryIds as $categoryId) {
            $counts[$categoryId] = (int) ($data[$categoryId] ?? 0);
        }

        return $counts;
    }

    /**
     * @param int[] $categoryIds
     * @return array<int, int>
     */
    public function getCategoryTopicCounts(array $categoryIds): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('t.topic_cat_id, COUNT(t.topic_id)')
            ->from('allianceboard_topics', 't')
            ->where('t.topic_cat_id IN (:categoryIds)')
            ->groupBy('t.topic_cat_id')
            ->setParameter('categoryIds', $categoryIds, ArrayParameterType::INTEGER)
            ->fetchAllKeyValue();

        $counts = [];
        foreach ($categoryIds as $categoryId) {
            $counts[$categoryId] = (int) ($data[$categoryId] ?? 0);
        }

        return $counts;
    }

    /**
     * @return Category[]
     */
    public function getCategories(int $allianceId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('allianceboard_cat')
            ->where('cat_alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->orderBy('cat_order')
            ->addOrderBy('cat_name')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new Category($row), $data);
    }

    /**
     * @return int[]
     */
    public function getCategoryIds(int $allianceId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('cat_id')
            ->from('allianceboard_cat')
            ->where('cat_alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->fetchAllAssociative();

        return array_map(fn (array $row) => (int) $row['cat_id'], $data);
    }

    public function getCategory(int $categoryId, int $allianceId): ?Category
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('allianceboard_cat')
            ->where('cat_id = :catId')
            ->andWhere('cat_alliance_id = :allianceId')
            ->setParameters([
                'catId' => $categoryId,
                'allianceId' => $allianceId,
            ])
            ->fetchAssociative();

        return $data !== false ? new Category($data) : null;
    }

    public function addCategory(string $name, string $description, int $order, string $bullet, int $allianceId): int
    {
        $this->createQueryBuilder('q')
            ->insert('allianceboard_cat')
            ->values([
                'cat_name' => ':name',
                'cat_desc' => ':description',
                'cat_order' => ':order',
                'cat_bullet' => ':bullet',
                'cat_alliance_id' => ':allianceId',
            ])
            ->setParameters([
                'name' => $name,
                'description' => $description,
                'order' => $order,
                'bullet' => $bullet,
                'allianceId' => $allianceId,
            ])
            ->executeQuery();

        return (int) $this->getConnection()->lastInsertId();
    }

    public function updateCategory(int $categoryId, string $name, string $description, int $order, string $bullet, int $allianceId): void
    {
        $this->createQueryBuilder('q')
            ->update('allianceboard_cat')
            ->set('cat_name', ':name')
            ->set('cat_desc', ':description')
            ->set('cat_order', ':order')
            ->set('cat_bullet', ':bullet')
            ->where('cat_id = :id')
            ->andWhere('cat_alliance_id = :allianceId')
            ->setParameters([
                'name' => $name,
                'description' => $description,
                'order' => $order,
                'bullet' => $bullet,
                'id' => $categoryId,
                'allianceId' => $allianceId,
            ])
            ->executeQuery();
    }

    public function deleteAllCategories(int $allianceId): void
    {
        $categoryIds = $this->createQueryBuilder('q')
            ->select('cat_id')
            ->from('allianceboard_cat')
            ->where('cat_alliance_id = :allianceId')
            ->setParameters([
                'allianceId' => $allianceId,
            ])
            ->fetchFirstColumn();

        if (count($categoryIds) === 0) {
            return;
        }

        $topicIds = $this->createQueryBuilder('q')
            ->select('topic_id')
            ->from('allianceboard_topics')
            ->where('topic_cat_id IN (:categoryIds)')
            ->setParameter('categoryIds', $categoryIds, ArrayParameterType::INTEGER)
            ->fetchFirstColumn();

        if (count($topicIds) > 0) {
            $this->createQueryBuilder('q')
                ->delete('allianceboard_posts')
                ->where('post_topic_id IN (:topicId)')
                ->setParameter('topicId', $topicIds, ArrayParameterType::INTEGER)
                ->executeQuery();

            $this->createQueryBuilder('q')
                ->delete('allianceboard_topics')
                ->where('topic_id IN (:topicId)')
                ->setParameter('topicId', $topicIds, ArrayParameterType::INTEGER)
                ->executeQuery();
        }

        $this->createQueryBuilder('q')
            ->delete('allianceboard_catranks')
            ->where('cr_cat_id IN (:categoryIds)')
            ->setParameter('categoryIds', $categoryIds, ArrayParameterType::INTEGER)
            ->executeQuery();

        $this->createQueryBuilder('q')
            ->delete('allianceboard_cat')
            ->where('cat_alliance_id = :allianceId')
            ->setParameters([
                'allianceId' => $allianceId,
            ])
            ->executeQuery();
    }

    public function deleteCategory(int $categoryId, int $allianceId): void
    {
        $topicIds = $this->createQueryBuilder('q')
            ->select('topic_id')
            ->from('allianceboard_topics')
            ->where('topic_cat_id = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->fetchFirstColumn();

        if (count($topicIds) > 0) {
            $this->createQueryBuilder('q')
                ->delete('allianceboard_posts')
                ->where('post_topic_id IN (:topicId)')
                ->setParameter('topicId', $topicIds, ArrayParameterType::INTEGER)
                ->executeQuery();

            $this->createQueryBuilder('q')
                ->delete('allianceboard_topics')
                ->where('topic_id IN (:topicId)')
                ->setParameter('topicId', $topicIds, ArrayParameterType::INTEGER)
                ->executeQuery();
        }

        $this->createQueryBuilder('q')
            ->delete('allianceboard_cat')
            ->where('cat_id = :catId')
            ->andWhere('cat_alliance_id = :allianceId')
            ->setParameters([
                'catId' => $categoryId,
                'allianceId' => $allianceId,
            ])
            ->executeQuery();
    }
}
